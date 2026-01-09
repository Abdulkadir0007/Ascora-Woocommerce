jQuery(function ($) {
  function ascoraFetch(url, push = true) {
    const $products = $("#ascora-products");
    $products.addClass("loading");
    const offsetTop = $("#ascora-products").offset().top - 100;

    $("html, body").animate(
      {
        scrollTop: offsetTop,
      },
      600
    );
    const ajaxUrl =
      url + (url.includes("?") ? "&" : "?") + "ascora_ajax=fragments";

    $.get(ajaxUrl, function (html) {
      const $response = $("<div>").html(html);

      $products.html($response.find("li.product"));

      $(".wc-ascora-pagination").replaceWith(
        $response.find(".wc-ascora-pagination")
      );

      // history URL always clean
      if (push) {
        history.pushState(null, "", url);
      }
    }).always(function () {
      $products.removeClass("loading");
    });
  }

  /* =========================
   * Pagination (FINAL FIX)
   * ========================= */
  $(document).on("click", ".wc-ascora-pagination a", function (e) {
    e.preventDefault();

    const clickedUrl = new URL(this.href, window.location.origin);
    const currentUrl = new URL(window.location.href);

    // persist current params (orderby, filters)
    currentUrl.searchParams.forEach((value, key) => {
      if (!clickedUrl.searchParams.has(key)) {
        clickedUrl.searchParams.set(key, value);
      }
    });

    // 🚫 NEVER keep ajax param
    clickedUrl.searchParams.delete("ascora_ajax");

    // ✅ build clean URL
    let finalUrl = clickedUrl.origin + clickedUrl.pathname;

    if ([...clickedUrl.searchParams].length) {
      finalUrl += "?" + clickedUrl.searchParams.toString();
    }

    ascoraFetch(finalUrl);
  });

  /* =========================
   * Sorting
   * ========================= */
  $(document).on("change", "#ascora-sort", function () {
    const url = new URL(window.location.href);

    url.searchParams.set("orderby", this.value);
    url.searchParams.delete("paged"); // reset to page 1 (correct way)

    ascoraFetch(url.toString());
  });

  /* =========================
   * Back / Forward browser
   * ========================= */
  window.addEventListener("popstate", function () {
    ascoraFetch(location.href, false);
  });

  /* =========================
   * Apply price filter widgets
   * ========================= */
  $(document).on("click", "#ascora-price-apply", function (e) {
    e.preventDefault();
    $("#ascora-filter-reset").fadeIn(150);
    const min = $("#ascora-price-min").val();
    const max = $("#ascora-price-max").val();

    const url = new URL(window.location.href);

    url.searchParams.set("min_price", min);
    url.searchParams.set("max_price", max);
    url.searchParams.delete("paged");
    url.searchParams.delete("ascora_ajax");

    ascoraFetch(url.toString());
  });

  /* -------------------------
   * PRICE SLIDER INIT (SAFE)
   * ------------------------- */

  const maxPrice = parseInt($("#ascora-price-max").val(), 10);

  if ($("#ascora-price-slider").length && maxPrice) {
    $("#ascora-price-slider").slider({
      range: true,
      min: 0,
      max: maxPrice,
      values: [0, maxPrice],
      slide: function (event, ui) {
        $("#ascora-price-min").val(ui.values[0]);
        $("#ascora-price-max").val(ui.values[1]);
        $("#ascora-price-min-val").text("$" + ui.values[0]);
        $("#ascora-price-max-val").text("$" + ui.values[1]);
      },
    });
  }
  // RESET FILTER (CLEAN & SAFE)
  // RESET FILTER (CLEAN & SAFE)
  jQuery(document).on("click", "#ascora-filter-reset", function (e) {
    e.preventDefault();

    const minDefault = 0;
    // const maxDefault =
    //   parseInt($("#ascora-price-max").attr("data-max"), 10) || 0;
    const maxDefault = parseInt($("#ascora-price-max").val(), 10);
    // 1. input fields
    $("#ascora-price-min").val(minDefault);
    $("#ascora-price-max").val(maxDefault);

    // 2. display text
    $("#ascora-price-min-val").text("$" + minDefault);
    $("#ascora-price-max-val").text("$" + maxDefault);

    // 3. slider
    if ($("#ascora-price-slider").hasClass("ui-slider")) {
      $("#ascora-price-slider").slider("values", [minDefault, maxDefault]);
    }

    // 4. clean URL & fetch
    const url = new URL(window.location.href);
    ["min_price", "max_price", "paged", "ascora_ajax"].forEach((p) =>
      url.searchParams.delete(p)
    );
    ascoraFetch(url.toString());

    // 5. hide button
    $("#ascora-filter-reset").fadeOut(150);
  });
});

document.addEventListener("DOMContentLoaded", function () {
  const widgets = document.querySelectorAll(
    ".ascora-shop-sidebar .product_list_widget li"
  );

  widgets.forEach((li) => {
    const link = li.querySelector("a");
    if (!link) return;

    const img = link.querySelector("img");
    const title = link.querySelector(".product-title");
    const rating = li.querySelector(".star-rating");
    const price = li.querySelector(".amount");

    /* ---------- Image wrapper ---------- */
    const imgWrap = document.createElement("div");
    imgWrap.className = "ascora-product-img";

    if (img) imgWrap.appendChild(img);

    /* ---------- Meta wrapper ---------- */
    const metaWrap = document.createElement("div");
    metaWrap.className = "ascora-product-meta";

    if (title) metaWrap.appendChild(title);
    if (rating) metaWrap.appendChild(rating);
    if (price) metaWrap.appendChild(price);

    /* ---------- rebuild LI ---------- */
    const href = link.getAttribute("href");

    li.innerHTML = "";
    li.appendChild(imgWrap);
    li.appendChild(metaWrap);

    /* ---------- make whole card clickable (optional) ---------- */
    if (href) {
      li.style.cursor = "pointer";
      li.addEventListener("click", function () {
        window.location.href = href;
      });
    }
  });
});
