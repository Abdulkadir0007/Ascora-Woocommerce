jQuery(function ($) {

    $("#s").on("keyup", function () {

        let keyword = $(this).val();
        let cat = $("#product_cat").val();

        if (keyword.length < 2) {
            $("#s").removeClass("loading");
            $(".as-search-results").html("").hide();
            return;
        }

        $.ajax({
            url: ascora_ajax.ajax_url,
            type: "GET",
            data: {
                action: "ascora_ajax_search",
                keyword: keyword,
                cat: cat
            },

            beforeSend: function () {
                // add class for spinner
                $("#s").addClass("loading");

                $(".as-search-results")
                    .html("<div class='loading'>Loading...</div>")
                    .show();
            },

            success: function (res) {

                // remove loading spinner
                $("#s").removeClass("loading");

                let html = "<ul>";

                if (res.length > 0) {
                    res.forEach(item => {
                        html += `
                            <li onclick="window.location='${item.url}'">
                                <img src="${item.img}">
                                <div>
                                    <strong>${item.title}</strong><br>
                                    <span>${item.price}</span>
                                </div>
                            </li>
                        `;
                    });
                } else {
                    html += "<li>No products found</li>";
                }

                html += "</ul>";

                $(".as-search-results").html(html).show();
            }
        });

    });

});
