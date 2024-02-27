(function ($) {
  "use strict";

  $(document).ready(function () {
    // Default Form submit
    $("#neonAddForm").submit(function (event) {
      event.preventDefault();

      var returnPage = "admin.php?page=neon-sign-customize";
      if ($("#action").val() == "add_new_additional") {
        returnPage =
          returnPage + "&func=additional" + "&type=" + $("#return").val();
      } else {
        returnPage = returnPage + "&func=" + $("#return").val();
      }
      //showToast("Error submitting data", "danger");
      ajax_insert(this, $("#action").val(), returnPage);
    });

    // Fonts Form submit
    $("#neonAddFont").submit(function (event) {
      event.preventDefault();
      var checkboxes = $('input[name="price_tier[]"]:checked');
      var returnPage =
        "admin.php?page=neon-sign-customize" + "&func=" + $("#return").val();
      if (checkboxes.length === 0) {
        alert("Please choose at least one price tier checkbox.");
      } else {
        ajax_insert(this, $("#action").val(), returnPage);
      }
    });

    // DELETE
    $(".nsc-delete-item").click(function () {
      ajax_delete($(this).data("item-id"), $(this).data("action"));
    });

    // CHANGE SETTING
    $(".nsc-change-item").click(function () {
      $.ajax({
        type: "POST",
        url: ajaxurl, // WordPress AJAX URL
        data: {
          action: $(this).data("action"),
          item_id: $(this).data("item-id"),
        },
        success: function (response) {
          location.reload();
        },
        error: function () {
          showToast("Error change item.", "danger");
        },
      });
    });

    // checkbox
    $(".nsc-checkbox").change(function () {
      if ($(this).hasClass("simple_font")) {
        $(".complex_font, .very_complex_font").prop("checked", false);
      } else if ($(this).hasClass("complex_font")) {
        $(".simple_font, .very_complex_font").prop("checked", false);
      } else if ($(this).hasClass("very_complex_font")) {
        $(".simple_font, .complex_font").prop("checked", false);
      }
    });

    // AJAX
    function ajax_delete($itemId, $action) {
      if (confirm("Are you sure!?")) {
        $.ajax({
          type: "POST",
          url: ajaxurl, // WordPress AJAX URL
          data: {
            action: $action,
            item_id: $itemId,
          },
          success: function (response) {
            location.reload(); // Reload the page after deletion
          },
          error: function () {
            showToast("Error deleting item", "danger");
          },
        });
      }
    }

    function ajax_insert($form, $action, $return_page) {
      var formData = new FormData($form);
      formData.append("action", $action);

      $.ajax({
        type: "POST",
        url: ajaxurl, // WordPress AJAX URL
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
          //alert(response);
          var data = JSON.parse(response);

          if (data.message) {
            if (data.message == "success") {
              window.location.href = $return_page;
            } else {
              showToast(data.message, "success");
            }
          }
        },
        error: function () {
          showToast("Error submitting data", "danger");
        },
      });
    }

    function showToast(message, type) {
      const toastContainer = $("#toastContainer");

      const toast = $(
        '<div class="toast" role="alert" aria-live="assertive" aria-atomic="true">'
      )
        .addClass(`bg-${type} text-white`)
        .attr("data-bs-delay", "10000")
        .append($('<div class="toast-body">').text(message));

      toastContainer.append(toast);

      const bsToast = new bootstrap.Toast(toast[0]);
      bsToast.show();
    }
  });
})(jQuery);
