var id = "";
              $("#table").find("td").click(function(){
                if ($(this).index() != 5){
                  id = $(this).parent().attr("id");
                  $.ajax({
                      type: "POST",
                      url: 'getdata.php',
                      data: {UserID: id},
                      success: function(data){
                          var result = $.parseJSON(data);
                          var email = result[0];
                          var phone = result[1];
                          var school = result[2];
                          var status = result[3];
                          var gpa_o = result[4];
                          var gpa_i = result[5];
                          var name = result[6];
                          var note = result[7];

                          var date = result[8];
                          var start = result[9];
                          var end = result[10];
                          var round = result[11];
                          var code = result[12];
                          $("#view-title").html(name);
                          $("#view-email").val(email);
                          $("#view-phone").val(phone);
                          $("#view-gpa-o").val(gpa_o);
                          $("#view-gpa-i").val(gpa_i);
                          $("#view-school").val(school);
                          $("#view-comments").val(note);
                          $("#view-status").val(code);
                          $("#int-date").val(date);
                          $("#int-start").val(start);
                          $("#int-end").val(end);
                          $("#int-round").val(round);
                          $("#view-modal").modal("show");
                      }
                  });
                }
              });
              $("#view-submit").click(function(e){
                $.ajax({
                  type: "POST",
                  url: 'scheduleinterview.php',
                  data: "id=" + id + "&" + $("#view-form").serialize(),
                  success: function(data){
                    $("#view-success").addClass("alert alert-success alert-dismissible");
                    $("#view-success").append("Changes saved.");
                    window.setTimeout(function(){
                      $("#view-success").removeClass("alert alert-success alert-dismissible");
                      $("#view-success").empty();
                    }, 5000);
                  } 
                });
                e.preventDefault();
              });