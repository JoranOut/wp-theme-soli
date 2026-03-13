(function () {
  var pageContent = document.getElementsByClassName("page-content")[0];
  if (!pageContent) return;

  var paragraphs = Array.prototype.slice.call(
    pageContent.getElementsByTagName("p")
  );

  // Title image: preserve existing behavior (image-wrapper + click-to-open)
  for (var i = 0; i < paragraphs.length; i++) {
    if (paragraphs[i].classList.contains("title_image")) {
      var img = paragraphs[i].getElementsByTagName("img")[0];
      if (img) {
        paragraphs[i].classList.add("image-wrapper");
        (function (p, img) {
          var anchor = img.parentElement.tagName === "A" ? img.parentElement : null;
          p.addEventListener("click", function (e) {
            e.preventDefault();
            window.open(anchor ? anchor.href : img.src);
          });
        })(paragraphs[i], img);
      }
    }
  }

  // Group consecutive image-bearing paragraphs (excluding title_image)
  var groups = [];
  var currentGroup = [];

  for (var i = 0; i < paragraphs.length; i++) {
    var p = paragraphs[i];
    var imgs = p.getElementsByTagName("img");
    if (imgs.length > 0 && !p.classList.contains("title_image")) {
      currentGroup.push(p);
    } else {
      if (currentGroup.length > 0) {
        groups.push(currentGroup);
        currentGroup = [];
      }
    }
  }
  if (currentGroup.length > 0) {
    groups.push(currentGroup);
  }

  for (var g = 0; g < groups.length; g++) {
    var group = groups[g];

    // Collect all images across paragraphs in this group
    var allImages = [];
    for (var j = 0; j < group.length; j++) {
      var imgs = group[j].getElementsByTagName("img");
      for (var k = 0; k < imgs.length; k++) {
        allImages.push({
          img: imgs[k],
          anchor: imgs[k].parentElement.tagName === "A" ? imgs[k].parentElement : null,
        });
      }
    }

    // Single image: keep existing behavior
    if (allImages.length < 2) {
      for (var j = 0; j < group.length; j++) {
        group[j].classList.add("image-wrapper");
        (function (p) {
          var img = p.getElementsByTagName("img")[0];
          var anchor = img && img.parentElement.tagName === "A" ? img.parentElement : null;
          p.addEventListener("click", function (e) {
            e.preventDefault();
            var url = anchor ? anchor.href : img.src;
            window.open(url);
          });
        })(group[j]);
      }
      continue;
    }

    // Multiple images: build carousel
    buildCarousel(group, allImages);
  }

  function getBestSrc(img) {
    var srcset = img.getAttribute("srcset");
    if (!srcset) return img.src;

    var candidates = srcset.split(",").map(function (entry) {
      var parts = entry.trim().split(/\s+/);
      var url = parts[0];
      var width = parts[1] ? parseInt(parts[1], 10) : 0;
      return { url: url, width: width };
    });

    // Prefer 1024w, then 768w, then largest available
    var preferred = [1024, 768];
    for (var i = 0; i < preferred.length; i++) {
      for (var j = 0; j < candidates.length; j++) {
        if (candidates[j].width === preferred[i]) return candidates[j].url;
      }
    }

    // Fallback: largest
    candidates.sort(function (a, b) { return b.width - a.width; });
    return candidates[0] ? candidates[0].url : img.src;
  }

  function buildCarousel(paragraphs, images) {
    var carousel = document.createElement("div");
    carousel.className = "image-carousel";
    carousel.setAttribute("tabindex", "0");

    var track = document.createElement("div");
    track.className = "carousel-track";

    for (var i = 0; i < images.length; i++) {
      var slide = document.createElement("div");
      slide.className = "carousel-slide" + (i === 0 ? " active" : "");

      var placeholder = document.createElement("img");
      placeholder.className = "carousel-placeholder";
      placeholder.src = images[i].img.src;
      placeholder.alt = images[i].img.alt || "";

      var fullImg = document.createElement("img");
      fullImg.className = "carousel-full";
      fullImg.setAttribute("data-src", getBestSrc(images[i].img));
      fullImg.alt = images[i].img.alt || "";

      // Store full-size URL for click-to-enlarge
      var fullUrl = images[i].anchor ? images[i].anchor.href : images[i].img.src;
      slide.setAttribute("data-full-url", fullUrl);

      slide.appendChild(placeholder);
      slide.appendChild(fullImg);
      track.appendChild(slide);
    }

    carousel.appendChild(track);

    // Navigation buttons
    var prevBtn = document.createElement("button");
    prevBtn.className = "carousel-prev";
    prevBtn.setAttribute("aria-label", "Previous");
    prevBtn.innerHTML = "&#8249;";
    carousel.appendChild(prevBtn);

    var nextBtn = document.createElement("button");
    nextBtn.className = "carousel-next";
    nextBtn.setAttribute("aria-label", "Next");
    nextBtn.innerHTML = "&#8250;";
    carousel.appendChild(nextBtn);

    // Dots
    var dotsContainer = document.createElement("div");
    dotsContainer.className = "carousel-dots";
    for (var i = 0; i < images.length; i++) {
      var dot = document.createElement("span");
      dot.className = "carousel-dot" + (i === 0 ? " active" : "");
      dot.setAttribute("data-index", i);
      dotsContainer.appendChild(dot);
    }
    carousel.appendChild(dotsContainer);

    // Counter
    var counter = document.createElement("span");
    counter.className = "carousel-counter";
    counter.textContent = "1 / " + images.length;
    carousel.appendChild(counter);

    // Insert carousel before the first paragraph, then remove all paragraphs in the group
    paragraphs[0].parentNode.insertBefore(carousel, paragraphs[0]);
    for (var i = 0; i < paragraphs.length; i++) {
      paragraphs[i].parentNode.removeChild(paragraphs[i]);
    }

    // Carousel state and controls
    var currentIndex = 0;
    var slides = track.getElementsByClassName("carousel-slide");
    var dots = dotsContainer.getElementsByClassName("carousel-dot");
    var total = images.length;

    function goTo(index) {
      if (index < 0) index = total - 1;
      if (index >= total) index = 0;

      currentIndex = index;
      track.style.transform = "translateX(-" + (index * 100) + "%)";

      for (var i = 0; i < slides.length; i++) {
        slides[i].classList.toggle("active", i === index);
      }
      for (var i = 0; i < dots.length; i++) {
        dots[i].classList.toggle("active", i === index);
      }
      counter.textContent = (index + 1) + " / " + total;

      // Lazy load active and next slide
      lazyLoadSlide(index);
      if (index + 1 < total) lazyLoadSlide(index + 1);
    }

    function lazyLoadSlide(index) {
      var fullImg = slides[index].querySelector(".carousel-full");
      if (!fullImg || !fullImg.getAttribute("data-src")) return;
      var placeholder = slides[index].querySelector(".carousel-placeholder");
      var src = fullImg.getAttribute("data-src");
      fullImg.removeAttribute("data-src");
      fullImg.addEventListener("load", function () {
        fullImg.classList.add("loaded");
        if (placeholder) placeholder.style.display = "none";
      });
      fullImg.src = src;
      // Fallback: if image was cached, load event may have already fired
      if (fullImg.complete) {
        fullImg.classList.add("loaded");
        if (placeholder) placeholder.style.display = "none";
      }
    }

    // Navigation events
    prevBtn.addEventListener("click", function (e) {
      e.stopPropagation();
      goTo(currentIndex - 1);
    });

    nextBtn.addEventListener("click", function (e) {
      e.stopPropagation();
      goTo(currentIndex + 1);
    });

    // Dot navigation
    dotsContainer.addEventListener("click", function (e) {
      if (e.target.classList.contains("carousel-dot")) {
        goTo(parseInt(e.target.getAttribute("data-index"), 10));
      }
    });

    // Keyboard navigation
    carousel.addEventListener("keydown", function (e) {
      if (e.key === "ArrowLeft") {
        e.preventDefault();
        goTo(currentIndex - 1);
      } else if (e.key === "ArrowRight") {
        e.preventDefault();
        goTo(currentIndex + 1);
      }
    });

    // Touch/swipe support
    var touchStartX = 0;
    var touchEndX = 0;

    carousel.addEventListener("touchstart", function (e) {
      touchStartX = e.changedTouches[0].screenX;
    }, { passive: true });

    carousel.addEventListener("touchend", function (e) {
      touchEndX = e.changedTouches[0].screenX;
      var diff = touchStartX - touchEndX;
      if (Math.abs(diff) > 50) {
        if (diff > 0) {
          goTo(currentIndex + 1);
        } else {
          goTo(currentIndex - 1);
        }
      }
    });

    // Click to enlarge (on the slide image, not on nav buttons)
    track.addEventListener("click", function (e) {
      var slide = e.target.closest(".carousel-slide");
      if (slide) {
        var fullUrl = slide.getAttribute("data-full-url");
        if (fullUrl) window.open(fullUrl);
      }
    });

    // IntersectionObserver: only start loading when carousel enters viewport
    if ("IntersectionObserver" in window) {
      var observer = new IntersectionObserver(
        function (entries) {
          if (entries[0].isIntersecting) {
            goTo(0); // triggers lazy load of first + second slide
            observer.disconnect();
          }
        },
        { rootMargin: "200px" }
      );
      observer.observe(carousel);
    } else {
      // Fallback: load immediately
      goTo(0);
    }
  }
})();
