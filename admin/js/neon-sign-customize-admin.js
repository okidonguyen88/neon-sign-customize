(function ($) {
  "use strict";

  $(document).ready(function () {
    $("#fontUploadForm").submit(function (event) {
      event.preventDefault();

      var font_name = $("#font_name").val();
      var font_short = $("#font_short").val();
      var font_url = $("#font_url").val();
      var extra_price = $("#extra_price").val();
      var task = $("#task").val();
      var font_id = $("#font_id").val();

      // Validation checks here
      var isValid = true;

      if (font_name.trim() === "") {
        isValid = false;
        $("#font_name").addClass("is-invalid");
      } else {
        $("#font_name").removeClass("is-invalid");
      }

      // More validation checks for other fields...

      if (isValid) {
        $.ajax({
          type: "POST",
          url: ajaxurl, // WordPress AJAX URL
          data: {
            action: "add_new_font",
            font_name: font_name,
            font_short: font_short,
            font_url: font_url,
            extra_price: extra_price,
            task: task,
            font_id: font_id,
          },
          success: function (response) {
            var data = JSON.parse(response);
            if (data.message) {
              window.location.href = data.message;
            }
          },
          error: function () {
            alert("Error submitting data.");
          },
        });
      }
    });

    $(".delete-item").click(function () {
      var itemId = $(this).data("item-id");

      if (confirm("Are you sure!?")) {
        $.ajax({
          type: "POST",
          url: ajaxurl, // WordPress AJAX URL
          data: {
            action: "delete_font",
            item_id: itemId,
          },
          success: function (response) {
            var data = JSON.parse(response);
            location.reload(); // Reload the page after deletion
          },
          error: function () {
            alert("Error deleting item.");
          },
        });
      }
    });
  });
})(jQuery);
