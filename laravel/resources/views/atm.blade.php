<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Title</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <style>
        .margin_nav {
            margin-bottom: 30px;
        }
        .margin_group_list {
            margin-top:30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <ul class="nav justify-content-end margin_nav">
            <li class="nav-item">
                <a class="nav-link active" href="#">Active</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Link</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Link</a>
            </li>
            <li class="nav-item">
                <a class="nav-link disabled" href="#">Disabled</a>
            </li>
        </ul>
        
        <div class="row">
            <div class="col-lg-12">
                <form id="form_withdraw" action="#" method="post" enctype="multipart/form-data">
                    <div class="form-group row">
                        <label for="inputPassword" class="col-sm-2 col-form-label">WITHDRAW</label>
                        <div class="col-sm-10">
                            <input type="number" min="20" max="500000" class="form-control" id="withdraw" placeholder="please input withdraw number">
                        </div>
                    </div>
                    <div class="text-right">
                        <button type="button" onClick="getOptionWithdraw();" class="btn btn-success">Check option Withdraw</button>
                    </div>
                    <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
                </form>
            </div>
            <div class="col-lg-8 offset-lg-2">
                <div class="margin_group_list">
                    <ul class="list-group" id="group_lists_basic">
                    </ul>
                    <ul class="list-group margin_group_list" id="group_lists_option">
                    </ul>
                </div>
            </div>
        </div>
    </div>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div id="error_message">
            <!-- Message Error -->
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>

<script type="text/javascript">
    $(document).ready(function(){
        
    });

    function getOptionWithdraw() {
        var withdrawNumber = $('#withdraw').val();
        var res;
        if (withdrawNumber < 20) {
            $('#error_message').empty().append('Please input min 20');
            $('#exampleModal').modal('show');
        } else {
            curlData(withdrawNumber);
        }
    }

    function curlData(withdrawNumber) {
        $.ajax({
            method: "POST",
            url: "/checkoption_withdraw",
            data: {
                withdraw: withdrawNumber
            },
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: function(res) {
                if(res.status) {
                    // Basic
                    var dataBasic = res.response.basic;
                    var messageBasic = '';
                    for (var key in dataBasic) {
                        if (dataBasic.hasOwnProperty(key)) {
                            messageBasic += `Banknote: ${key} : ${dataBasic[key]} <br/>`;
                        }
                    }
                    $('#group_lists_basic').empty().append(`<li class="list-group-item"><span class="badge badge-primary badge-pill">Basic</span><br/>--------------------------<br/>${messageBasic}</li>`)

                    var dataOption = res.response.option;
                    $('#group_lists_option').empty();
                    dataOption.map(function(value, index){
                        var messageOption = '';
                        for (var key in value) {
                            if (value.hasOwnProperty(key)) {
                                messageOption += `Banknote: ${key} : ${value[key]} <br/>`;
                            }
                        }
                        $('#group_lists_option').append(`<li class="list-group-item"><span class="badge badge-primary badge-pill">Option ${index + 1}</span><br/>--------------------------<br/>${messageOption}</li>`)
                    });
                } else {
                    $('#exampleModalLabel').empty().append('Messages');
                    $('#error_message').empty().append(res.message);
                    $('#exampleModal').modal('show');
                }
            }
        });
    }
</script>
</html>