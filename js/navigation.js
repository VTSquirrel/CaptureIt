function hideActive(sub){
  $(".active-content").hide();
  $(".active-content").removeClass("active-content");
  $(".active").removeClass("active");
  if (sub){
    $(".nav-sub").hide();
  }
}

$("#dash").click(function(){
  hideActive(true);

  $(this).addClass("active");
  $("#dash-content").addClass("active-content");
  $("#dash-content").show();
  $("#dash-sub").addClass("active");
  return false;
});

$("#dash-sub").click(function(){
  hideActive(true);

  $(this).addClass("active");
  $("#dash").addClass("active");

  $("#dash-content").addClass("active-content");
  $("#dash-content").show();
  return false;
});

$("#profile").click(function(){
  hideActive(true);

  $("#prof-content").show();

  $(this).addClass("active");
  $("#prof-content").addClass("active-content");
  $("#edit-profile").addClass("active");
  return false;
});

$("#edit-profile").click(function(){
  hideActive(true);

  $("#prof-content").show();
  $(this).addClass("active");
  $("#prof-content").addClass("active-content");
  $("#profile").addClass("active");
  return false;
});

$("#manage-app").click(function(){
  hideActive(true);

  $(this).addClass("active");
  $("#app").addClass("active");
  $("#app-content").addClass("active-content");
  $("#app-content").show();
  return false;
});

$("#app").click(function(){
  hideActive(true);

  $(this).addClass("active");
  $("#manage-app").addClass("active");
  $("#app-content").addClass("active-content");
  $("#app-content").show();
  return false;
});
$("#applicants").click(function(){
  hideActive(true);

  $(this).addClass("active");
  $("#applicants-content").addClass("active-content");
  $("#applicants-content").show();
  return false;
});
$("#analysis").click(function(){
  hideActive(false);

  $(this).addClass("active");
  $("#bubble-nav").show();
  $("#analysis-content").addClass("active-content");
  $("#analysis-content").show();

  $(".nav-sub").show();
  return false;
});
$("#bubble-nav").click(function(){
  hideActive(false);
  $(this).addClass("active");
  $("#analysis-content").addClass("active-content");
  $("#analysis-content").show();
  $("#bubble").addClass("active-content");
  $("#bubble").show();
  return false;
});
$("#liveschool-nav").click(function(){
  hideActive(false);

  $(this).addClass("active");
  $("#analysis-content").addClass("active-content");
  $("#analysis-content").show();
  $("#live-school").addClass("active-content");
  $("#live-school").show();
  return false;
});
$("#gpabyschool-nav").click(function(){
  hideActive(false);

  $(this).addClass("active");
  $("#analysis-content").addClass("active-content");
  $("#analysis-content").show();
  $("#gpa-by-major-by-school").addClass("active-content");
  $("#gpa-by-major-by-school").show();
  return false;
});
$("#wgpabyschool-nav").click(function(){
  hideActive(false);

  $(this).addClass("active");
  $("#analysis-content").addClass("active-content");
  $("#analysis-content").show();
  $("#weighted-gpa-by-school").addClass("active-content");
  $("#weighted-gpa-by-school").show();
  return false;
});
$("#individual-nav").click(function(){
  hideActive(false);

  $(this).addClass("active");
  $("#analysis-content").addClass("active-content");
  $("#analysis-content").show();
  $("#individual-overview").addClass("active-content");
  $("#individual-overview").show();
  return false;
});
$("#interview").click(function(){
  hideActive(true);

  $(this).addClass("active");
  $("#interview-content").addClass("active-content");
  $("#interview-content").show();
  return false;
});