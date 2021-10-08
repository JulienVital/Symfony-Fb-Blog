let menuIsOpen=false
openNav = document.getElementById("nav").addEventListener("click", function(){
  document.getElementById("myNav").style.width = "100%";
  document.getElementById("overlay-content").style.visibility = "visible";
  document.getElementById("overlay-content").style.opacity = 1;
  menuIsOpen=true


});
document.getElementById("closeNav").addEventListener("click", closeNav)

function closeNav (){
  document.getElementById("myNav").style.width ="0%";
  document.getElementById("overlay-content").style.visibility = "hidden";
  document.getElementById("overlay-content").style.opacity = 0;
  menuIsOpen=false
};

document.addEventListener('keydown', function(event){
	if(event.key === "Escape" && menuIsOpen){
		closeNav()
	}
});

document.getElementById("myNav").addEventListener('click', function(e){
  if(menuIsOpen && !document.getElementById('overlay-content').contains(e.target)){
    closeNav()
  }
});