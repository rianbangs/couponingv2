<div class="card col-12">
    <div class="card-content">
        <div class="card-body">    
            <div class="form-body">
                    
                <div class="row">
                    <!-- <div class="col-2">
                        <div class="form-group has-icon-left">
                            <label for="status_sel" style="color: black; font-weight: bold;">STATUS</label>
                            <div class="position-relative">
                                <div class="form-control-icon">
                                    <i data-feather="plus-square"></i>
                                </div>
                                <select id="status_sel" class="form-control" onchange="listUsers()">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option> 
                                </select>
                            </div>
                        </div>
                    </div> -->

                    <div class="col-2" style="padding-top: 20px;">
                        <button class="btn btn-primary mr-1 mb-1" onclick="addUserModal()">ADD USER</button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive" style="padding-top: 20px;">
                            <table id="user-table" class="table table-striped">
                                <thead style="text-align: center; background-color: #d2eaf7; color: black;">
                                    <th>FIRSTNAME</th>
                                    <th>LASTNAME</th>
                                    <th>USERNAME</th>
                                    <th>STORE</th>
                                    <th>ACCESS</th>
                                    <th>ACTION</th>  
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                   
            </div>
        </div>
    </div>
 </div>

 <!-- User Modal -->
<div class="modal fade" id="userModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title" id=userModalTitle></h4>
                <button type="button" class="close" onclick="closeUserModal()">
                    <i data-feather="x"></i>
                </button>
            </div>

            <div class="modal-body" id="userModalBody">
                <div class="row">
                    <div class="col-6">  
                        <div class="form-group has-icon-left">
                            <label for="user_fname" style="color: black; font-weight: bold;">FIRST NAME</label>
                            <div class="position-relative">
                                <div class="form-control-icon">
                                    <i data-feather="user"></i>
                                </div>
                                <input id="user_fname" type="text" class="form-control" placeholder="Input First Name" autocomplete="off" oninput="regex1(this)">
                                <div></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">  
                        <div class="form-group has-icon-left">
                            <label for="user_lname" style="color: black; font-weight: bold;">LAST NAME</label>
                            <div class="position-relative">
                                <div class="form-control-icon">
                                    <i data-feather="user"></i>
                                </div>
                                <input id="user_lname" type="text" class="form-control" placeholder="Input Last Name" autocomplete="off" oninput="regex1(this)">
                                <div></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-6">  
                        <div class="form-group has-icon-left">
                            <label for="user_uname" style="color: black; font-weight: bold;">USERNAME</label>
                            <div class="position-relative">
                                <div class="form-control-icon">
                                    <i data-feather="user-check"></i>
                                </div>
                                <input id="user_uname" type="text" class="form-control" placeholder="Input Username" autocomplete="off" oninput="regex2(this)">
                                <div></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">  
                        <div id="user_access_block" class="form-group has-icon-left" style="display: none;">
                            <label for="user_access" style="color: black; font-weight: bold;">ACCESS</label>
                            <div class="position-relative">
                                <div class="form-control-icon">
                                    <i data-feather="unlock"></i>
                                </div>
                                <select id="user_access" class="form-control" onchange="setStoreSelect()">
                                    <option value="ordering">ORDERING</option>
                                    <option value="liquidation">LIQUIDATION</option>
                                    <option value="IAD">IAD</option>
                                    <option value="accounting">ACCOUNTING</option>
                                    <option value="mpdi">MPDI</option>
                                </select>
                            </div>
                        </div>
                        <div id="user_password_block" class="form-group has-icon-left" style="display: none;">
                            <label for="user_password" style="color: black; font-weight: bold;">OLD PASSWORD</label>
                            <div class="position-relative">
                                <div class="form-control-icon">
                                    <i data-feather="lock"></i>
                                </div>
                                <input id="user_password" type="password" class="form-control" placeholder="Input Old Password" autocomplete="off" oninput="regex2(this)">
                            </div>
                        </div>
                    </div>
                    
                </div>

                <div class="row">
                    <div class="col-6">  
                        <div id="user_store_block" class="form-group has-icon-left" style="display: none;">
                            <label for="user_store" style="color: black; font-weight: bold;">STORE</label>
                            <div class="position-relative">
                                <div class="form-control-icon">
                                    <i data-feather="minus-square"></i>
                                </div>
                                <select id="user_store" class="form-control">
                                    <option value="13">ICM</option>
                                    <option value="15">ASC-DUM</option>
                                    <option value="16">ASC</option>
                                    <option value="17">TAL</option>
                                    <option value="18">PM</option>
                                    <option value="19">AC</option>
                                </select>
                            </div>
                        </div>

                        <div id="user_new_password_block" class="form-group has-icon-left" style="display: none;">
                            <label for="user_new_password" style="color: black; font-weight: bold;">NEW PASSWORD</label>
                            <div class="position-relative">
                                <div class="form-control-icon">
                                    <i data-feather="lock"></i>
                                </div>
                                <input id="user_new_password" type="password" class="form-control" placeholder="Input New Password" autocomplete="off" oninput="regex2(this)">
                            </div>
                        </div>
                    </div>

                    <div class="col-6">  
                        <div id="user_con_password_block" class="form-group has-icon-left" style="display: none;">
                            <label for="user_con_password" style="color: black; font-weight: bold;">CONFIRM PASSWORD</label>
                            <div class="position-relative">
                                <div class="form-control-icon">
                                    <i data-feather="lock"></i>
                                </div>
                                <input id="user_con_password" type="password" class="form-control" placeholder="Input Confirm Password" autocomplete="off" oninput="regex2(this)">
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" id="userModalBtn" class="btn btn-primary" onclick="userModalBtnFunc()">
                
                </button>       
            </div>
        
        </div>
    </div>
</div>