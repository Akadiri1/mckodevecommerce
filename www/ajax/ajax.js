
// document.getElementById('idAdd').addEventListener("click",function(e) {
//   here();
// },false)

function ajaxPost(url,param,cb){

var param = JSON.stringify(param);

var xhr = new XMLHttpRequest();
xhr.onreadystatechange = function(){
  if(xhr.readyState == 4 && xhr.status == 200){
return cb("NULL",xhr.responseText);
// console.log(result);
  }
}

xhr.open("POST",url,true);
xhr.setRequestHeader("Content-Type","application/json");
xhr.send(param);

}


function ajaxGet(url,cb){



var xhr = new XMLHttpRequest();
xhr.onreadystatechange = function(){
  if(xhr.readyState == 4 && xhr.status == 200){
return cb("NULL",xhr.responseText);
// console.log(result);
  }
}

xhr.open("GET",url,true);
xhr.setRequestHeader("Content-Type","application/json");
xhr.send();
}





// function here(){
//   // console.log("here");
//   var arr = {
//               "values":{
//                   // "input_title":"Banji",
//                   // "text_body":12,
//                   // "hash_id":213456565433554,
//                   // "text_body":213456565433554,
//                   // "image_1":213456565433554,
//                   // "visibility":"show",
//                   // "created_by":"show",
//                   "lga_vector_code":"ddddd",
//                   "lga":"ddddd",
//                   "date_created":"2019-12-12",
//                   "time_created":"22:22:34",
//                   // "thumbnail":"22:22:34",
//                   // "thum":"sdfghjik",
//                 },
//                 "data":"lga"
//             };
//   ajaxPost("/add",arr,function(err,response) {
//     console.log(response);
//     var res = JSON.parse(response);
//   if(res.success){
//     alert("SuccessFull");
//   }else{
//     alert("Failed");
//   }
//     });
// }
// function hereView(){
//   // console.log("here");
//   var arr = {
//               "where":{"id":1},
//                 "data":"lga"
//             };
//   ajaxPost("/read",arr,function(err,response) {
//     console.log(response);
//     var res = JSON.parse(response);
//   if(res.success){
//         console.log(res);
//     alert("SuccessFull");
//   }else{
//     alert("Failed");
//   }
//     });
// }
// function herePut(){
//   // console.log("here");
//   var arr = {
//               "values":{
//                   "input_title":"Lorem ipsum dolor sit amet, consectetur ",
//                   "text_body":"Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.",
//                   // "hash_id":213456565433554,
//                   // "text_body":213456565433554,
//                   // "image_1":213456565433554,
//                   "visibility":"show",
//                   // "created_by":"show",
//                   // "lga_vector_code":"ddddd",
//                   // "lga":"ddddd",
//                   // "date_created":"2019-12-12",
//                   // "time_created":"22:22:34",
//                   // "thumbnail":"22:22:34",
//                   // "thum":"sdfghjik",
//                 },
//               "where":{"input_title":"Tade","hash_id":"156475038655091"},
//               "data":"policy_polls"
//             };
//   ajaxPost("/put",arr,function(err,response) {
//     console.log(response);
//     var res = JSON.parse(response);
//   if(res.success){
//         console.log(res);
//     alert("SuccessFull");
//   }else{
//     alert("Failed");
//   }
//     });
// }
// function hereDelete(){
//   // console.log("here");
//   var arr = {
//               "where":{"input_title":"Shade","hash_id":"156475038655091"},
//               "data":"policy_polls"
//             };
//   ajaxPost("/delete",arr,function(err,response) {
//     console.log(response);
//     var res = JSON.parse(response);
//   if(res.success){
//         console.log(res);
//     alert("SuccessFull");
//   }else{
//     alert("Failed");
//   }
//     });
// }
