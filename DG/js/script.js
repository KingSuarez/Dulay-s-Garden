
function myFunction() {
   var x = document.getElementById("myDIV");
   var x1 = document.getElementById("myDIV1");
   var x2 = document.getElementById("myDIV2");
   var x3 = document.getElementById("myDIV3");
   var x4 = document.getElementById("myDIV4");
 
   if (x.style.display === "block" && x1.style.display === "block" && x2.style.display === "block" && x3.style.display === "block" && x4.style.display === "block") {
     x.style.display = "none";
     x1.style.display = "none";
     x2.style.display = "none";
     x3.style.display = "none";
     x4.style.display = "none";
     document.getElementById("main").style.background = "#EB6123";
 
   } else {
     x.style.display = "block";
     x1.style.display = "block";
     x2.style.display = "block";
     x3.style.display = "block";
     x4.style.display = "block";
     document.getElementById("main").style.background = "#343F66";
     document.getElementById("myDIV").style.background = "#343F66";
     document.getElementById("myDIV1").style.background = "#343F66";
     document.getElementById("myDIV2").style.background = "#343F66";
     document.getElementById("myDIV3").style.background = "#343F66";
     document.getElementById("myDIV4").style.background = "#343F66";
   }
 }
 
 function evaluation() {
   var e = document.getElementById("eval");
   var e1 = document.getElementById("eval1");
 
   if (e.style.display === "block" && e1.style.display === "block") {
     e.style.display = "none";
     e1.style.display = "none";
     document.getElementById("main2").style.background = "#EB6123";
 
   } else {
     e.style.display = "block";
     e1.style.display = "block";
     document.getElementById("main2").style.background = "#343F66";
     document.getElementById("eval").style.background = "#343F66";
     document.getElementById("eval1").style.background = "#343F66";
   }
 }
 