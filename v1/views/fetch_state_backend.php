<?php
header('Content-Type: application/json');
$country = trim($_POST['countryVal'] ?? $_GET['countryVal'] ?? '');

// State lists by country code
$states = [
    'NG' => ['Abia','Adamawa','Akwa Ibom','Anambra','Bauchi','Bayelsa','Benue','Borno','Cross River',
             'Delta','Ebonyi','Edo','Ekiti','Enugu','FCT - Abuja','Gombe','Imo','Jigawa','Kaduna',
             'Kano','Katsina','Kebbi','Kogi','Kwara','Lagos','Nasarawa','Niger','Ogun','Ondo','Osun',
             'Oyo','Plateau','Rivers','Sokoto','Taraba','Yobe','Zamfara'],
    'US' => ['Alabama','Alaska','Arizona','Arkansas','California','Colorado','Connecticut','Delaware',
             'Florida','Georgia','Hawaii','Idaho','Illinois','Indiana','Iowa','Kansas','Kentucky',
             'Louisiana','Maine','Maryland','Massachusetts','Michigan','Minnesota','Mississippi',
             'Missouri','Montana','Nebraska','Nevada','New Hampshire','New Jersey','New Mexico',
             'New York','North Carolina','North Dakota','Ohio','Oklahoma','Oregon','Pennsylvania',
             'Rhode Island','South Carolina','South Dakota','Tennessee','Texas','Utah','Vermont',
             'Virginia','Washington','West Virginia','Wisconsin','Wyoming'],
    'GB' => ['England','Scotland','Wales','Northern Ireland'],
    'CA' => ['Alberta','British Columbia','Manitoba','New Brunswick','Newfoundland and Labrador',
             'Northwest Territories','Nova Scotia','Nunavut','Ontario','Prince Edward Island',
             'Quebec','Saskatchewan','Yukon'],
    'GH' => ['Ahafo','Ashanti','Bono','Bono East','Central','Eastern','Greater Accra','North East',
             'Northern','Oti','Savannah','Upper East','Upper West','Volta','Western','Western North'],
    'KE' => ['Nairobi','Mombasa','Kisumu','Nakuru','Eldoret','Thika','Malindi','Kitale','Garissa','Kakamega'],
    'ZA' => ['Eastern Cape','Free State','Gauteng','KwaZulu-Natal','Limpopo','Mpumalanga',
             'Northern Cape','North West','Western Cape'],
    'AU' => ['New South Wales','Victoria','Queensland','Western Australia','South Australia',
             'Tasmania','Australian Capital Territory','Northern Territory'],
    'DE' => ['Baden-Württemberg','Bavaria','Berlin','Brandenburg','Bremen','Hamburg','Hesse',
             'Mecklenburg-Vorpommern','Lower Saxony','North Rhine-Westphalia','Rhineland-Palatinate',
             'Saarland','Saxony','Saxony-Anhalt','Schleswig-Holstein','Thuringia'],
    'FR' => ['Île-de-France','Auvergne-Rhône-Alpes','Hauts-de-France','Nouvelle-Aquitaine',
             'Occitanie','Grand Est','Normandie','Pays de la Loire','Bretagne','Bourgogne-Franche-Comté',
             'Centre-Val de Loire','Provence-Alpes-Côte d\'Azur','Corse'],
    'IN' => ['Andhra Pradesh','Arunachal Pradesh','Assam','Bihar','Chhattisgarh','Goa','Gujarat',
             'Haryana','Himachal Pradesh','Jharkhand','Karnataka','Kerala','Madhya Pradesh',
             'Maharashtra','Manipur','Meghalaya','Mizoram','Nagaland','Odisha','Punjab','Rajasthan',
             'Sikkim','Tamil Nadu','Telangana','Tripura','Uttar Pradesh','Uttarakhand','West Bengal'],
];

if (empty($country) || !isset($states[$country])) {
    echo json_encode(['success' => []]);
    exit;
}

$result = array_map(function($s) { return ['state' => $s]; }, $states[$country]);
echo json_encode(['success' => $result]);
