var $ = jQuery;

var boiwalaAjaxFilterSearch = $("#boiwala-ajax-filter-search");
if (boiwalaAjaxFilterSearch.length) {
    var boiwalaAjaxFilterSearchForm = boiwalaAjaxFilterSearch.find("form");

    boiwalaAjaxFilterSearchForm.submit(function(e) {
        e.preventDefault();

        if (boiwalaAjaxFilterSearchForm.find("#search").val().length !== 0) {
            var search = boiwalaAjaxFilterSearchForm.find("#search").val();
        }
        if (boiwalaAjaxFilterSearchForm.find("#year").val().length !== 0) {
            var year = boiwalaAjaxFilterSearchForm.find("#year").val();
        }
        if (boiwalaAjaxFilterSearchForm.find("#isbn").val().length !== 0) {
            var isbn = boiwalaAjaxFilterSearchForm.find("#isbn").val();
        }
        if (boiwalaAjaxFilterSearchForm.find("#categories").val().length !== 0) {
            var categories = boiwalaAjaxFilterSearchForm.find("#categories").val();
        }
        if (boiwalaAjaxFilterSearchForm.find("#price").val().length !== 0) {
            var price = boiwalaAjaxFilterSearchForm.find("#price").val();
        }

        var data = {
            action: "boiwala_ajax_filter_search",
            search: search,
            year: year,
            isbn: isbn,
            categories: categories,
            price: price
        };

        $.ajax({
            url: ajax_info.ajax_url,
            data: data,
            success: function(response) {
                boiwalaAjaxFilterSearch.find("ul").empty();
                if (response) {
                    for (var i = 0; i < response.length; i++) {
                        var bookHtml = `
                            <li id='book-${response[i].id}'>
                                <a href='${response[i].permalink}' title='${response[i].title}'>
                                    <img src='${response[i].poster}' alt='${response[i].title}' />
                                    <div class='book-info'>
                                        <h4>${response[i].title}</h4>
                                        <p>Year: ${response[i].year}</p>
                                        <p>ISBN: ${response[i].isbn}</p>
                                        <p>Price: ${response[i].price}</p>
                                        <p>Categories: ${response[i].categories}</p>
                                    </div>
                                </a>
                            </li>
                        `;
                        boiwalaAjaxFilterSearch.find("ul").append(bookHtml);
                    }

                } else {
                    var html = "<li class='no-result'>Books not found. Try a different filter or search keyword</li>";
                    boiwalaAjaxFilterSearch.find("ul").append(html);
                }
            }
        });
    });
}
