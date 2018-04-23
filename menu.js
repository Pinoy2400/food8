async function hentMenu() {
    let menufil = await fetch("menu.html")
    let menu = await menufil.text();
    document.querySelector("[data-menu]").innerHTML = menu;

}

//document.addEventListener("DOMContentLoaded", hentMenu);
window.addEventListener("load", hentMenu);






//DROPDOWN MENU

// Når brugeren klikker på knappen, skiftes der mellem at gemme og vise dropdownindholdet.
function myDropdown() {
    document.getElementById("myDropdown").classList.toggle("show");
}



// BURGERMENU
        document.querySelector(".burger").addEventListener("click", toggleMenu);
        // document.querySelector(".skjulMenu").addEventListener("click", toggleMenu);


        function toggleMenu() {
            document.querySelector(".burger").classList.toggle("change");
            document.querySelector("nav").classList.toggle("show");
        };