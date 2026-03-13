// ======================= NAVIGATION TOGGLE =======================
const hamburger = document.querySelector('.hamburger');
const navLinks = document.querySelector('.nav-links');
const nav = document.querySelector('nav');

if (hamburger && navLinks) {
  hamburger.addEventListener('click', () => {
    navLinks.classList.toggle('active');
    hamburger.classList.toggle('open');
  });

  document.querySelectorAll('.nav-links a').forEach(link => {
    link.addEventListener('click', () => {
      navLinks.classList.remove('active');
      hamburger.classList.remove('open');
    });
  });
}

// ======================= NAVBAR SCROLL EFFECT =======================
if (nav) {
  window.addEventListener('scroll', () => {
    nav.classList.toggle('scrolled', window.scrollY > 50);
  });
}

// ======================= FADE-IN ON SCROLL =======================
const fadeElements = document.querySelectorAll('.fade-up');

if (fadeElements.length) {
  const fadeObserver = new IntersectionObserver(
    entries => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('show');
          fadeObserver.unobserve(entry.target); // prevent re-triggering
        }
      });
    },
    { threshold: 0.25 }
  );

  fadeElements.forEach(el => fadeObserver.observe(el));
}

// ======================= SMOOTH SCROLL =======================
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
  anchor.addEventListener('click', e => {
    const targetId = anchor.getAttribute('href');
    const targetEl = document.querySelector(targetId);

    if (targetEl) {
      e.preventDefault();
      window.scrollTo({
        top: targetEl.offsetTop - 70, // account for fixed nav
        behavior: 'smooth'
      });
    }
  });
});

// ======================= FEATURED IMAGE SLIDER =======================
const imageCards = document.querySelectorAll('.featured-images .image-card');
const prevBtn = document.getElementById('prevBtn');
const nextBtn = document.getElementById('nextBtn');
let currentFeatured = 0;
let featuredInterval;

function showFeaturedSlide(index) {
  if (!imageCards.length) return;
  imageCards.forEach((card, i) => {
    card.classList.toggle('active', i === index);
  });
  currentFeatured = index;
}

function startFeaturedInterval() {
  clearInterval(featuredInterval);
  featuredInterval = setInterval(() => {
    showFeaturedSlide((currentFeatured + 1) % imageCards.length);
  }, 5000);
}

if (imageCards.length) {
  showFeaturedSlide(currentFeatured);
  startFeaturedInterval();

  [prevBtn, nextBtn].forEach((btn, i) => {
    if (btn) {
      btn.addEventListener('click', () => {
        const nextIndex = i === 0
          ? (currentFeatured - 1 + imageCards.length) % imageCards.length
          : (currentFeatured + 1) % imageCards.length;
        showFeaturedSlide(nextIndex);
        startFeaturedInterval();
      });
    }
  });
}

// ======================= PRODUCTS FILTER / SORT / SEARCH =======================
// ======================= PRODUCTS FILTER / SORT / SEARCH =======================
function updateCategoryVisibility() {
  const categories = document.querySelectorAll(".category-section");
  let overallVisibleCount = 0;

  categories.forEach(section => {
    const productsInCat = section.querySelectorAll(".product-card");
    let visibleInCat = 0;

    productsInCat.forEach(p => {
      if (p.style.display !== "none") visibleInCat++;
    });

    section.style.display = visibleInCat > 0 ? "block" : "none";
    overallVisibleCount += visibleInCat;
  });

  const noResults = document.getElementById("no-results");
  if (noResults) noResults.style.display = overallVisibleCount === 0 ? "block" : "none";
}

function filterProducts() {
  const category = document.getElementById("category-filter")?.value || "all";
  const priceRange = document.getElementById("price-filter")?.value || "all";
  const products = document.querySelectorAll(".product-card");

  products.forEach(product => {
    const productCategory = product.dataset.category;
    const productPrice = parseFloat(product.dataset.price);
    const categoryMatch = category === "all" || productCategory === category;
    const priceMatch =
      priceRange === "all" ||
      (priceRange === "0-100" && productPrice < 100) ||
      (priceRange === "100-200" && productPrice >= 100 && productPrice <= 200) ||
      (priceRange === "200+" && productPrice > 200);

    const visible = categoryMatch && priceMatch;
    product.style.display = visible ? "block" : "none";
  });

  updateCategoryVisibility();
}

function sortProducts() {
  const sortValue = document.getElementById("sort-filter")?.value || "default";
  const categories = document.querySelectorAll('.products-container'); // All the individual grids

  if (!categories.length) return;

  categories.forEach(container => {
    const products = Array.from(container.getElementsByClassName("product-card"));
    let sorted = [...products];

    if (sortValue === "name") {
      sorted.sort((a, b) =>
        a.querySelector("h3").textContent.localeCompare(b.querySelector("h3").textContent)
      );
    } else if (sortValue === "price-low") {
      sorted.sort((a, b) => parseFloat(a.dataset.price) - parseFloat(b.dataset.price));
    } else if (sortValue === "price-high") {
      sorted.sort((a, b) => parseFloat(b.dataset.price) - parseFloat(a.dataset.price));
    }

    // Reattach in sorted order within their respective layout grids
    sorted.forEach(p => container.appendChild(p));
  });
}

function searchProducts() {
  const query = document.getElementById("search-input")?.value.toLowerCase() || "";
  const products = document.querySelectorAll(".product-card");

  products.forEach(product => {
    const name = product.querySelector("h3").textContent.toLowerCase();
    const visible = name.includes(query);
    product.style.display = visible ? "block" : "none";
  });

  updateCategoryVisibility();
}

// ======================= GALLERY SLIDER =======================
const galleryWrapper = document.querySelector(".gallery-slide");
const galleryImages = document.querySelectorAll(".gallery-slide img");
const prevGallery = document.querySelector(".prev");
const nextGallery = document.querySelector(".next");
let currentGallery = 0;
let galleryInterval;

function showGallerySlide(index) {
  if (!galleryWrapper || !galleryImages.length) return;

  const total = galleryImages.length;
  if (index >= total) currentGallery = 0;
  else if (index < 0) currentGallery = total - 1;
  else currentGallery = index;

  const slideWidth = galleryImages[0].offsetWidth + 40;
  galleryWrapper.style.transform = `translateX(-${currentGallery * slideWidth}px)`;
}

function startGalleryInterval() {
  clearInterval(galleryInterval);
  galleryInterval = setInterval(() => {
    showGallerySlide(currentGallery + 1);
  }, 4000);
}

if (galleryWrapper && galleryImages.length) {
  showGallerySlide(currentGallery);
  startGalleryInterval();

  [prevGallery, nextGallery].forEach((btn, i) => {
    if (btn) {
      btn.addEventListener("click", () => {
        const nextIndex = i === 0
          ? currentGallery - 1
          : currentGallery + 1;
        showGallerySlide(nextIndex);
        startGalleryInterval();
      });
    }
  });
}

// Dropdown Toggle
const userIcon = document.getElementById('userIcon');
const userDropdown = document.getElementById('userDropdown');

userIcon.addEventListener('click', (e) => {
  e.preventDefault();
  userDropdown.classList.toggle('show');
});

// Close dropdown when clicking outside
document.addEventListener('click', (e) => {
  if (!e.target.closest('.user-menu')) {
    userDropdown.classList.remove('show');
  }
});
// mainest.js
document.addEventListener("DOMContentLoaded", () => {
  // Use event delegation for dynamically added product cards
  document.body.addEventListener("click", (e) => {
    if (e.target.classList.contains("add-to-cart")) {
      const button = e.target;
      const productCard = button.closest(".product-card");
      const productName = productCard.querySelector("h3").textContent;
      // Remove currency symbol if present
      const productPrice = productCard.querySelector(".price").textContent.replace(/[^\d.]/g, '');
      const productImg = productCard.querySelector("img").getAttribute("src");

      // Create product object
      const product = {
        name: productName,
        price: productPrice,
        image: productImg,
        quantity: 1
      };

      fetch('add_to_cart.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        credentials: 'same-origin',
        body: JSON.stringify(product),
      })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            alert(`${productName} added to cart!`);
          } else {
            alert("Error: " + data.message);
            if (data.message === "User not logged in") {
              window.location.href = "login.html";
            }
          }
        })
        .catch((error) => {
          console.error('Error:', error);
          alert("An error occurred. Please try again.");
        });
    }
  });

  // Logout functionality
  const logoutBtn = document.getElementById("logoutBtn");
  if (logoutBtn) {
    logoutBtn.addEventListener("click", (e) => {
      // Clear storage first
      sessionStorage.clear();
      localStorage.clear();

      // Manual redirect to logout.php
      e.preventDefault();
      window.location.href = "logout.php";
    });
  }
});
