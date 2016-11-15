function load_project(){
  var proj_name = prompt("Please enter to load project name", " ");

  if (proj_name == null){

  }
  else if (proj_name == ""){

      alert("no null name. plz");
      load_project();
  }
  else {
    document.load_project_form.load_proj_name.value = proj_name;
    document.getElementById("load_project_form").submit();//form submit
  }


}
