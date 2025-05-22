function randomNumber(min, max) {
  return Math.round(Math.random() * (max - min)) + min;
}

function setPersonImg(url) {
  $("#person-img").attr("src", url);
}

$('.mobile-header-menu').click(function () {
  $('.sidebar').toggleClass('active')
})