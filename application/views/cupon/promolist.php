<div class="card col-12">
    <div class="card-content">
        <div class="card-body">    
            <div class="form-body">
                    
                <div class="row">
                    <div class="col-4">
                        <div class="form-group has-icon-left">
                            <label for="program_select" style="color: black; font-weight: bold;">PROGRAM</label>
                            <div class="position-relative">
                                 <select id="program_select" class="choices form-select" onchange="listPromoItems()">
                                    <option value="0">Patient Compliance</option>
                                    <option value="1">Health Plus</option> 
                                </select>
                                <div></div>
                            </div>
                        </div>

                        <hr>
                        
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group has-icon-left">
                                    <label for="item_code_tf" style="color: black; font-weight: bold;">ITEM CODE</label>
                                    <div class="position-relative">
                                        <div class="form-control-icon">
                                            <i data-feather="archive"></i>
                                        </div>
                                        <input id="item_code_tf" type="text" class="form-control">
                                        <div></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group has-icon-left">
                                    <label for="uom_sel" style="color: black; font-weight: bold;">UOM</label>
                                    <div class="position-relative">
                                        <div class="form-control-icon">
                                            <i data-feather="filter"></i>
                                        </div>
                                        <select id="uom_sel" class="form-control">
                                            <option value="PC">PC</option>
                                            <option value="BOT">BOT</option>
                                        </select>
                                        <div></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <button class="btn btn-primary mr-1 mb-1" onclick="savePromoItem()">SAVE</button>
                            </div>
                        </div>

                    </div>

                    <div class="col-8">
                        <div class="table-responsive" style="padding-top: 20px;">
                            <table id="promo-table" class="table table-striped">
                                <thead style="text-align: center; background-color: #d2eaf7; color: black;">
                                    <th width="80px">ITEM CODE</th>
                                    <th width="10px">UOM</th>
                                    <th>DESCRIPTION</th>
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
