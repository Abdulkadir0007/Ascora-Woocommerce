jQuery(function ($) {

    const $input = $("#s");
    const $results = $(".as-search-results");
    const $form = $("#as-wc-searchform");

    /**
     * LABEL CLICK → focus input + hide results
     */
    $("label[for='s']").on("click", function () {
        $input.trigger("focus");
        $results.hide();
    });


    /**
     * INPUT KEYUP → AJAX Search
     */
    $input.on("keyup", function () {

        let keyword = $(this).val();
        let cat = $("#product_cat").val();

        if (keyword.length < 2) {
            $(this).removeClass("loading");
            $results.html("").hide();
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
                $input.addClass("loading");
                $results.html("<div class='loading'>Loading...</div>").show();
            },

            success: function (res) {
                $input.removeClass("loading");

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
                            </li>`;
                    });
                } else {
                    html += "<li>No products found</li>";
                }

                html += "</ul>";

                $results.html(html).show();
            }
        });
    });


    /**
     * CLICK OUTSIDE → hide results
     */
    $(document).on("click", function (e) {
        if (
            !$(e.target).closest(".ascora-wc-product-search").length && 
            !$(e.target).is("#s")
        ) {
            $results.hide();
        }
    });
    // When input is cleared by browser "X" icon
    $("#s").on("input", function () {
        if ($(this).val().length < 1) {
            $(".as-search-results").html("").hide();
        }
    });
    /**
     * INPUT FOCUS → show results (if any text exists)
     */
    $input.on("focus", function () {
        if ($(this).val().length >= 2) {
            $results.show();
        }
    });

});



jQuery(function($){
    let frame;

    $(document).on('click', '.ascora-upload', function(e){
        e.preventDefault();

        let button = $(this);
        let input  = button.prev('input');
        let preview = button.next('.ascora-preview');

        if (frame) {
            frame.open();
            return;
        }

        frame = wp.media({
            title: 'Select Image',
            button: { text: 'Use Image' },
            multiple: false
        });

        frame.on('select', function () {
            let attachment = frame.state().get('selection').first().toJSON();
            input.val(attachment.id);
            preview.html('<img src="'+attachment.url+'" style="max-width:80px;">');
        });

        frame.open();
    });
});