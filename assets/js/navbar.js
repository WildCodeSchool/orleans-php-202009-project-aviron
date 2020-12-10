window.openNav = function () {
    document.getElementById('mySidebar').style.width = document.documentElement.style.getPropertyValue('$navbar-width');
    document.getElementById('content').style.marginLeft = document.documentElement.style.getPropertyValue('$navbar-width');
};

window.closeNav = function () {
    document.getElementById('mySidebar').style.width = '0';
    document.getElementById('content').style.marginLeft = '0';
};
