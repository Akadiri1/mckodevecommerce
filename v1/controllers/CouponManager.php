<?php

class CouponManager {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Step 2: Records initial application during checkout.
     * Status is 'applied' — does not count against limits yet.
     */
    public function applyCoupon($couponId, $identifier, $invoiceId) {
        $stmt = $this->pdo->prepare("
            INSERT INTO ecommerce_coupon_usage (coupon_id, identifier, invoice_id, status, date_created, time_created)
            VALUES (?, ?, ?, 'applied', NOW(), NOW())
        ");
        return $stmt->execute([$couponId, $identifier, $invoiceId]);
    }

    /**
     * Step 3: Finalizes usage after payment confirmation.
     * Status becomes 'consumed' and counts against future validations.
     */
    public function useInvoiceCoupon($invoiceId) {
        $stmt = $this->pdo->prepare(
            "UPDATE ecommerce_coupon_usage SET status = 'consumed' WHERE invoice_id = ?"
        );
        return $stmt->execute([$invoiceId]);
    }

    /**
     * Dual Currency Validation: Uses NGN as master baseline for thresholds.
     */
    public function validateDual($code, $totalNgn, $totalUsd, $identifier) {
        $stmt = $this->pdo->prepare("SELECT * FROM ecommerce_coupon WHERE code = ? LIMIT 1");
        $stmt->execute([trim($code)]);
        $coupon = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$coupon) {
            return ['success' => false, 'message' => 'Invalid coupon code.'];
        }

        if ($totalNgn !== null && $coupon['min_cart_amount'] > 0 && $totalNgn < $coupon['min_cart_amount']) {
            return [
                'success' => false,
                'message' => 'Requirement of ₦' . number_format($coupon['min_cart_amount'], 2) . ' not met.'
            ];
        }

        return $this->checkUsageLimits($coupon, $identifier);
    }

    /**
     * Single Currency Validation.
     */
    public function validateSingle($code, $cartTotal, $identifier) {
        $stmt = $this->pdo->prepare("SELECT * FROM ecommerce_coupon WHERE code = ? LIMIT 1");
        $stmt->execute([trim($code)]);
        $coupon = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$coupon) {
            return ['success' => false, 'message' => 'Invalid coupon code.'];
        }

        if ($coupon['min_cart_amount'] > 0 && $cartTotal < $coupon['min_cart_amount']) {
            return ['success' => false, 'message' => 'Min. purchase requirement not met.'];
        }

        return $this->checkUsageLimits($coupon, $identifier);
    }

    /**
     * Dual Price Calculation. Returns null for currencies where the original total was null.
     */
    public function calculateDualFinalPrices($coupon, $totalNgn, $totalUsd) {
        $results = [
            'ngn' => ['new_total' => null, 'discount' => null],
            'usd' => ['new_total' => null, 'discount' => null]
        ];

        if ($totalNgn !== null && $totalNgn > 0) {
            $results['ngn']['new_total'] = $this->calculateSingleFinalPrice($coupon, $totalNgn);
            $results['ngn']['discount']  = $totalNgn - $results['ngn']['new_total'];
        }

        if ($totalUsd !== null && $totalUsd > 0) {
            if ($coupon['type'] === 'percentage') {
                $results['usd']['new_total'] = $this->calculateSingleFinalPrice($coupon, $totalUsd);
            } else {
                // Fixed: proportional discount for USD
                $ratio = ($coupon['value'] / $totalNgn);
                $discUsd = $ratio * $totalUsd;
                $results['usd']['new_total'] = max(0, $totalUsd - $discUsd);
            }
            $results['usd']['discount'] = $totalUsd - $results['usd']['new_total'];
        }

        return $results;
    }

    /**
     * Single Price Calculation.
     */
    public function calculateSingleFinalPrice($coupon, $total) {
        if ($total === null || $total <= 0) return null;

        $discount = ($coupon['type'] === 'percentage')
            ? ($coupon['value'] / 100) * $total
            : $coupon['value'];

        return max(0, $total - $discount);
    }

    /**
     * Internal: Checks global and per-user limits against 'consumed' status only.
     */
    private function checkUsageLimits($coupon, $identifier) {
        if ($coupon['max_global_usage'] > 0) {
            $global = $this->pdo->prepare(
                "SELECT COUNT(id) FROM ecommerce_coupon_usage WHERE coupon_id = ? AND status = 'consumed'"
            );
            $global->execute([$coupon['id']]);
            if ($global->fetchColumn() >= $coupon['max_global_usage']) {
                return ['success' => false, 'message' => 'Coupon global limit reached.'];
            }
        }

        if ($coupon['max_user_usage'] > 0) {
            $user = $this->pdo->prepare(
                "SELECT COUNT(id) FROM ecommerce_coupon_usage WHERE coupon_id = ? AND identifier = ? AND status = 'consumed'"
            );
            $user->execute([$coupon['id'], $identifier]);
            if ($user->fetchColumn() >= $coupon['max_user_usage']) {
                return ['success' => false, 'message' => 'You have already used this coupon.'];
            }
        }

        return ['success' => true, 'coupon' => $coupon];
    }
}
