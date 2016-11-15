var key_num = 0;
var key_attribute="";

//dynamically change selects
function change_key_num(value){
  key_num = value;
  show_key_select();
}

function change_key_attribute(value){
  key_attribute = value;
  show_key_select();
}

function show_key_select(){
  var form = document.getElementById('key_select');
  form.innerHTML = "";

  var column_list = <?php echo json_encode($column_list); ?>;
  var column_size = <?php echo $column_size; ?>;

  //key has 3 attribute
  if (key_attribute == 'latitude_longitude_time' || key_attribute == 'x_y_t'){
    for (var i = 0 ; i < key_num ; i ++){
      for (var j = 0 ; j < 3 ; j++){
        var selectList = document.createElement("select");
        selectList.name = 'key['.concat(i.toString(),'][',j.toString(),']');
        form.appendChild(selectList);
        for (var k = 0 ; k < column_size ; k++) {
          var option = document.createElement("option");
          option.value = k;
          option.text = column_list[k];
          selectList.appendChild(option);
        }

      }
      form.appendChild(document.createElement("br"));
    }
  }
  //key has 2 attribute
  else if ( key_attribute == 'latitude_longitude' || key_attribute == 'x_y' ){
    for (var i = 0 ; i < key_num ; i ++){
      for (var j = 0 ; j < 2 ; j++){
        var selectList = document.createElement("select");
        selectList.name = 'key['.concat(i.toString(),'][',j.toString(),']');
        form.appendChild(selectList);//move file to hdfs
        for (var k = 0 ; k < column_size ; k++) {
          var option = document.createElement("option");
          option.value = k;
          option.text = column_list[k];
          selectList.appendChild(option);
        }

      }
      form.appendChild(document.createElement("br"));
    }
  }
}
