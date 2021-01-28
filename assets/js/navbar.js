window.openNav = function () {
    document.getElementById('mySidebar').style.width = document.documentElement.style.getPropertyValue('$navbar-width');
    document.getElementById('content').style.marginLeft = document.documentElement.style.getPropertyValue('$navbar-width');
    setTimeout(() => {
        document.getElementById('heart').style.display = 'inline-block';
    }, 300);
};

window.closeNav = function () {
    document.getElementById('mySidebar').style.width = '0';
    document.getElementById('content').style.marginLeft = '64px';
    document.getElementById('heart').style.display = 'none';
};
