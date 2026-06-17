/***
 * @author : Hardee Singh
 * @email : hardeepvicky1@gmail.com
 * @created : 03-Nov-2023
 * 
 * Depends : constants, jquery, fontawesome
 */

jQuery.fn.extend({
    idataTable: function ()
    {
        const feature = "i-data-table";
        const css_classes = {
            search_block : 'search-block',

            search_input : 'search-input',
            search_opener : 'search-icon',
            search_clear : 'search-clear-icon',
            sort : "sort-icon",

            will_hide : "will-hide",
        };

        function log(msg)
        {
            msg = "data-table : " + msg;

            console.log(msg);
        }

        return this.each(function ()
        {
            var _table = $(this);

            if (_table.applyOnce(feature))
            {
                _table.find("> thead > tr > th").each(function (index, value)
                {
                    var _th = $(this);
                    var will_all_clear = _th.getBoolFromData("all-clear", false);
                    var will_search = _th.getBoolFromData("search", false);                    
                    var sort = _th.data("sort");

                    var old_html = _th.html();

                    var new_html = old_html;

                    if (will_search)
                    {
                        var search_icon_disable_css_class = "disable";
                        if (will_search)
                        {
                            search_icon_disable_css_class = "";
                        }

                        var clear_icon_disable_css_class = "disable";
                        if (will_search)
                        {
                            clear_icon_disable_css_class = "";
                        }

                        var sort_icon_disable_css_class = "disable";
                        if (sort)
                        {
                            sort_icon_disable_css_class = "";
                        }

                        new_html +=
                            `
                            <div class="${css_classes.search_block}">
                                <input class="${css_classes.search_input} i-data-table-visibility-hidden" type="text" data-col-index="${index}"/>
                                <div class="search-right-icons">
                                    <span class="${css_classes.search_opener} ${search_icon_disable_css_class}" title="${search_icon_disable_css_class}" data-col-index="${index}">
                                        <i class="${constants.fontawsome.icon.search}"></i>
                                    </span>
                                    <span class="${css_classes.search_clear}  ${clear_icon_disable_css_class}" title="${clear_icon_disable_css_class}" data-col-index="${index}">
                                        <i class="${constants.fontawsome.icon.cross}"></i>
                                    </span>
                                    <span class="${css_classes.sort} ${sort_icon_disable_css_class}" title="${sort_icon_disable_css_class}" data-col-index="${index}">
                                        <i class="${constants.fontawsome.icon.sort}"></i>
                                    </span>
                                </div>
                            </div>
                        `;
                    }
                    else if (sort)
                    {
                        new_html +=
                            `
                            <div class="${css_classes.search_block}">                                
                                <div class="search-right-icons" style="width:28px;">                                    
                                    <span class="${css_classes.sort}" data-col-index="${index}">
                                        <i class="${constants.fontawsome.icon.sort}"></i>
                                    </span>
                                </div>
                            </div>
                        `;
                    }

                    if (old_html.length != new_html.length)
                    {
                        //means apply new html and attch events on new html
                        _th.html(new_html);

                        if (will_search)
                        {
                            function search_apply(col_index)
                            {
                                log(`Col Index : ${col_index}`);

                                var _th = _table.find(`> thead > tr > th:eq(${col_index})`);

                                var search_text = _th.find(`.${css_classes.search_input}`).val();

                                search_text = search_text.trim().toLowerCase();

                                log(`Search Text : ${search_text}`);

                                var row_counter = 0, hide_counter = 0;
                                _table.find("> tbody > tr").each(function ()
                                {
                                    row_counter++;
                                    var _td = $(this).find(`> td:eq(${col_index})`);
                                    if (search_text)
                                    {
                                        var _td_text = _td.text().toLowerCase();

                                        if (_td_text.indexOf(search_text) < 0)
                                        {
                                            hide_counter++;
                                            _td.addClass(css_classes.will_hide);
                                        }
                                        else
                                        {
                                            _td.removeClass(css_classes.will_hide);
                                        }
                                    }
                                    else
                                    {
                                        _td.removeClass(css_classes.will_hide);
                                    }
                                });

                                log(`Rows : ${row_counter}, Hide Rows : ${hide_counter}`);

                                _table.find('> tbody > tr').removeClass("i-data-table-display-none");
                                _table.find('> tbody > tr').has(`td.${css_classes.will_hide}`).addClass("i-data-table-display-none");
                            }

                            _th.find(`.${css_classes.search_opener}`).click(function ()
                            {
                                var search_input = _th.find(`.${css_classes.search_input}`);

                                search_input.toggleClass("i-data-table-visibility-hidden");
                            });

                            _th.find(`.${css_classes.search_input}`).keyup(function (event)
                            {
                                var keycode = (event.keyCode ? event.keyCode : event.which);

                                var col_index = $(this).data("col-index");

                                if (keycode == 13)
                                {
                                    search_apply(col_index);
                                }

                                if (keycode == 27)
                                {
                                    $(this).val("");

                                    search_apply(col_index);

                                    $(this).addClass("i-data-table-visibility-hidden");
                                }
                            });

                            _th.find(`.${css_classes.search_clear}`).click(function ()
                            {
                                var search_input = $(this).closest("th").find(`.${css_classes.search_input}`);
                                search_input.val("");
                                search_input.addClass("i-data-table-visibility-hidden");

                                var col_index = $(this).data("col-index");
                                search_apply(col_index);
                            });
                        }

                        if (sort)
                        {
                            _th.find(`.${css_classes.sort}`).click(function ()
                            {
                                var col_index = $(this).data("col-index");
                                var sort_dir = $(this).attr("data-sort-dir");
                                console.log(sort_dir);
                                if (sort_dir)
                                {
                                    sort_dir = sort_dir == "asc" ? "desc" : "asc";
                                }
                                else
                                {
                                    sort_dir = "asc";
                                }

                                var list = [];
                                _table.find("> tbody > tr").each(function (row_index)
                                {
                                    var _td = $(this).find(`> td:eq(${col_index})`);

                                    _td_text = _td.text().toLowerCase();

                                    list.push({
                                        "row_index" : row_index,
                                        "text" : _td_text
                                    });
                                });

                                if (sort == "numeric")
                                {
                                    list.sort(function(a, b){
                                        if (a['text'] === b['text']) {
                                            return 0;
                                        }

                                        a['text'] = parseFloat(a['text']);
                                        b['text'] = parseFloat(b['text']);

                                        
                                        var r = a['text'] < b['text'] ? -1 : 1;

                                        return sort_dir == "asc" ? r : r * -1;
                                    });
                                }
                                else
                                {
                                    list.sort(function(a, b){
                                        if (a['text'] === b['text']) {
                                            return 0;
                                        }

                                        var r = a.text.localeCompare(b.text);
                                        
                                        return sort_dir == "asc" ? r : r * -1;
                                    });
                                }

                                var tr_html = "";
                                for (var i in list)
                                {
                                    var arr = list[i];

                                    var h = _table.find(`> tbody > tr:eq(${arr['row_index']})`)[0].outerHTML;

                                    tr_html += h;
                                }

                                _table.find(`> tbody`).html(tr_html);

                                $(this).attr("data-sort-dir", sort_dir);
                            });
                        }
                    }
                });
            }
        });
    },
});