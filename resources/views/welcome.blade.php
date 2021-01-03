@extends('master.app')

@section('content')
<div class="post mt-5">
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <span id='successMsg'></span>
                <h5>Post</h5>
                <table class="table table-striped" id="myTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        
                    </tbody>
                </table>
            </div>
            <div class="col-md-4">
                <h5>Create Post</h5>               
                <form name="dataForm">
                    <div class="form-group">
                        <label for="">Title</label>
                        <input name="title" type="text" class="form-control">
                        <span id='titleError'></span>
                    </div>
                    <div class="form-group">
                        <label for="">Description</label>
                        <input name="description" type="text" class="form-control">
                        <span id='descError'></span>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Submit</button>
                </form>
            </div>

            <!-- Modal -->
            <div class="modal fade hidebox" id="editModalCenter" tabindex="-1" role="dialog" aria-labelledby="editModel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title" id="editModel">Modal title</h5>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div>
                    <form name="editForm">
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="">Title</label>
                                <input name="title" type="text" class="form-control" required>
                                <span id='titleError'></span>
                            </div>
                            <div class="form-group">
                                <label for="">Description</label>
                                <input name="description" type="text" class="form-control" required>
                                <span id='descError'></span>
                            </div>
                        </div>
                  
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                      <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
                  </div>
                </div>
              </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    var titleList = document.getElementsByClassName('titleList');
    var descList = document.getElementsByClassName('descList');
    var idList = document.getElementsByClassName('idList');
    var btnList = document.getElementsByClassName('btnList');
    //Fatch
    axios.get('api/posts')
        .then(response => {
            var tableBody = document.getElementById('tableBody');

            response.data.forEach(item => {
                addPost(item);
            });
        })
        .catch(error => {

            console.log(error.response);

            if(error.response.statusText == "Not Found"){
                console.log(' " '+error.response.config.url+' " url 404 not found');
            }
            
        });

    //Create

        var dataForm = document.forms['dataForm'];
        var titleInput = dataForm['title'];
        var descriptionInput = dataForm['description'];

        dataForm.onsubmit = function(e){

            e.preventDefault();
            axios.post('api/posts', {
                title: titleInput.value,
                description: descriptionInput.value                  
            })
                .then(response => {                     

                    var titleErr = document.getElementById('titleError')
                    var descErr = document.getElementById('descError')

                    if(response.data.msg == "Data Created Successfully"){
                        showAlert(response.data.msg);
                        dataForm.reset();
                        addPost(response.data.posts)
                        titleErr.innerHTML = descErr.innerHTML = '';

                    }else {                        
                        titleErr.innerHTML =  titleInput.value == '' ? '<i class="text-danger">'+response.data.msg.title+'</i>' : '';
                        console.log(titleErr);

                        descErr.innerHTML =  descriptionInput.value == '' ? '<i class="text-danger">'+response.data.msg.description+'</i>' : '';                
                        console.log(descErr);
                        
                    }                  
                })
                .catch(error => console.log(error))
        }

        //Edit
        var editForm = document.forms['editForm'];
        var editTitle = editForm['title'];
        var editDesc = editForm['description'];
        var updateId , oldTitle;

        function editClick(postId) {
            updateId = postId;
            axios.get('api/posts/'+postId)            
            .then(response => { 
                editTitle.value = response.data.title;
                editDesc.value = response.data.description;

                oldTitle = response.data.title;
            })
            .catch(error => console.log(error))
        }

        //Update
        editForm.onsubmit = function(e) {
            e.preventDefault();

            axios.put('api/posts/'+updateId,{
                'title' : editTitle.value,
                'description' : editDesc.value
            })
            .then(response => {
                showAlert(response.data.msg);                
                $('.hidebox').modal('hide')

                for (var i = 0; i < titleList.length; i++) {
                   if(titleList[i].innerHTML == oldTitle){
                       titleList[i].innerHTML = editTitle.value;
                       descList[i].innerHTML = editDesc.value;
                   }                    
                }
               
            })
           
            .catch(error => console.log(error))

        }

        //Delect

        function deleteClick(postId) {
           if(confirm('Are you sure delete it?')){
            axios.delete('api/posts/'+postId)
            .then(response => { 
                showAlert(response.data.msg);
                console.log(response.data.postList.title);

                for (var i = 0; i < titleList.length; i++) {
                   if(titleList[i].innerHTML == response.data.postList.title) {
                        idList[i].style.display = titleList[i].style.display = descList[i].style.display = btnList[i].style.display = "none";
                   }                       
                }

            })
            .catch(error => console.log(error))
           }
        }

        //Helper Function

        function addPost(post) {
            tableBody.innerHTML += 
            '<tr>'+
            '<td class="idList">'+post.id+'</td>'+
            '<td class="titleList">'+post.title+'</td>'+
            '<td class="descList">'+post.description+'</td>'+
            '<td class="btnList">'+
            '<button class="btn btn-success btn-sm" data-toggle="modal" data-target="#editModalCenter" onclick="editClick('+post.id+')">Edit</button>'+
            '<button class="btn btn-danger btn-sm ml-2" onclick="deleteClick('+post.id+')">Delete</button>'+
            '</td>'+
            '</tr>'
        }

        function showAlert(msg) {
            document.getElementById('successMsg').innerHTML =
            '<div class="alert alert-success alert-dismissible fade show" role="alert">'+
            '<strong>'+msg+'</strong>'+
            '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
            '<span aria-hidden="true">&times;</span>'+
            '</button>'+'</div>';
        }
       

</script>
    
@endsection