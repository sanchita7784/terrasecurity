jQuery.fn.extend({
    browseItemGallery : function (obj)
    {
        if (typeof obj == "undefined")
        {
            obj = {};
        }
        
        if (typeof obj.postfix == "undefined")
        {
            obj.postfix = "-main";
        }

        if (typeof obj.opener == "undefined")
        {
            alert("Please pass opener in argument");
            return;
        }
        
        return this.each(function()
        {
            var _this = $(this);
            
            $(this).hide();
            
            var value = $(this).val();
            var multiple = $(this).attr("multiple");
            multiple = multiple ? 1 : 0;
                        
            var btn = "<div><span class='item-gallery-files-info' style='margin-left:5px;'></span><div>";
            
            $(this).parent().append(btn);

            var browseBtn = $(this).parent().find(".item-gallery-browse").first();
            
            var modal_id = 'item-gallery-modal-' + obj.postfix;
            
            var html = '<div class="modal full-width-model fade" id="' + modal_id  + '" tabindex="-1" role="basic" aria-hidden="true" style="display: none;">';
                    html += '<div class="modal-dialog">';
                        html += '<div class="modal-content">';
                            html += '<div class="modal-header">';
                                html += '<h5 class="modal-title">Item Gallery</h5>';
                                html += '<button type="button" class="btn-close" data-bs-dismiss="modal">';                                    
                                html += '</button>';        
                            html += '</div>';
                        html += '<div class="modal-body"> </div>';
                    html += '<div class="modal-footer">';
                        html += '<button class="btn btn-danger de-select">De-Select All</button>';
                        html += '<button class="btn btn-info select">Select</button>';
                    html += '</div>';
                html += '</div>';
            html += '</div>';
            html += '</div>';
            
            
            var gallery_modal = $("#" + modal_id);
            
            var list = value ? value.split(",") : [];
            var file_info = $(this).parent().find(".item-gallery-files-info");
            file_info.html(list.length + " files Selected");

            if (gallery_modal.length == 0)
            {
                $("body").append(html);
                gallery_modal = $("#" + modal_id);
            }

            gallery_modal.find(".modal-footer button.select").click(function()
            {
                var list = [];
                gallery_modal.find(".img-container.selected").each(function()
                {
                    var id = $(this).data("id");
                    list.push(id);
                });
                
                if (list.length == 0)
                {
                    bootbox.alert("Please select at least one Image");
                }
                else
                {
                    file_info.html(list.length + " files Selected");

                    _this.val(list.join(","));

                    $(gallery_modal).modal('hide');
                    
                    if (typeof obj.onSelect == "function")
                    {
                        obj.onSelect(list);
                    }
                }
            });
            
            gallery_modal.find(".modal-footer button.de-select").click(function()
            {
                $("#product-gallery-container .img-container").removeClass("selected");
                $("#product-gallery-container .img-container .selection-area").html("");
            });
        
            $(obj.opener).click(function ()
            {
                $(gallery_modal).find(".modal-body").load("/admin/items_gallery", {value : value, multiple : multiple});
                $(gallery_modal).modal('show');
            });
            
        });
    },
});