const hamburgerButton = document.getElementById('hamburger')
const navList = document.getElementById('nav-list')

function toggleButton () {
  navList.classList.toggle('show')
}

hamburgerButton.addEventListener('click', toggleButton)

const images = document.querySelectorAll('.image-inside-card')

for (const elmt of images) {
  const height = elmt.naturalHeight
  const ratio = elmt.naturalWidth / 180

  if (height / ratio > 295) {
    elmt.style.height = 295 + 'px'
  }
}
