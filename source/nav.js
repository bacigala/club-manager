
function dropdownButtonClicked(dropdownButton) {
    let width = (window.innerWidth > 0) ? window.innerWidth : screen.width;
    if (width < 900) {
        // mobile only
        let displayProperty = dropdownButton.nextElementSibling.style.getPropertyValue("display");
        if (displayProperty === "none" || displayProperty === "") {
            displayProperty = "block";
        } else {
            displayProperty = "none";
        }
        dropdownButton.nextElementSibling.style.display = displayProperty;
    }
}

function dropdownMenuHoverEnter(dropdown) {
    let width = (window.innerWidth > 0) ? window.innerWidth : screen.width;
    if (width >= 900) {
        // desktop only
        dropdown.firstElementChild.nextElementSibling.style.display = "block";
    }
}

function dropdownMenuHoverLeave(dropdown) {
    let width = (window.innerWidth > 0) ? window.innerWidth : screen.width;
    if (width >= 900) {
        // desktop only
        dropdown.firstElementChild.nextElementSibling.style.display = "none";
    }
}
