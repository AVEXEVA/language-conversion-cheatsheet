function json_to_bootstrap(link){
  $.ajax({
    url:"data.json",
    success:function(json){
      var data = eval(json);
      $(data).each(function(){
        $(link).append("<div class='row data-column'><div class='col data-key'><label class='data-key'>" + data.key + "</label></div><div class='data-value col'><input type='text' value='" + data.key + "' name='" + data.key + "' /></div></div>");
      });
    }
  });
};
