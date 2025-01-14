const images = document.querySelectorAll('.image');
const contentTitle = document.getElementById('content-title');
const contentDescription = document.getElementById('content-description');
let currentIndex = 0;

function showImage(index) {
    images.forEach((image, i) => {
        if (i === index) {
            image.style.display = 'block';
        } else {
            image.style.display = 'none';
        }
    });
}

function showContent(index) {
    const content = getContent(index);
    contentTitle.textContent = content.title;
}

function getContent(index) {
    const contents = [
        { title: 'Sports' },
        { title: 'Art'},
        { title: 'Music'},
        { title: 'Travel'},
        { title: 'Technology'},
        { title: 'Fitness'},
        { title: 'Gaming'}
    ];
    return contents[index];
}

function goToNextImage() {
    currentIndex = (currentIndex + 1) % images.length;
    showImage(currentIndex);
    showContent(currentIndex);
}

function goToPreviousImage() {
    currentIndex = (currentIndex - 1 + images.length) % images.length;
    showImage(currentIndex);
    showContent(currentIndex);
}

// Adding event listeners to all images
images.forEach(image => {
    image.addEventListener('click', goToNextImage);
});

document.getElementById('next-button').addEventListener('click', goToNextImage);
document.getElementById('prev-button').addEventListener('click', goToPreviousImage);

showImage(currentIndex);
showContent(currentIndex);

function nextpage(){
    window.location.href = "home.html";
}