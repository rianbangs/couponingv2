<?php
defined('BASEPATH') OR exit('No direct script access allowed');

//$route['default_controller'] 								= 'main_control';
$route['default_controller']                                = 'Mpdi_ctrl/mpdi_ui';
$route['404_override'] 										= '';
$route['translate_uri_dashes'] 								= FALSE;
//$route['(:any)'] 											= 'main_control/access_type/$1';
$route['(:any)']                                            = 'mpdi/Mpdi_ctrl/home';


//MPDI===============================================================================================================
$route['home']                                             = 'Mpdi_ctrl/home';
$route['mpdi_ui']                                          = 'Mpdi_ctrl/mpdi_ui';
$route['mpdi/get_si_details']                                   = 'Mpdi_ctrl/get_si_details';
//===================================================================================================================


//===========================CONSTRUCTION============================================================================
$route['construction/approved_request'] 					='construction/Construction_ctrl/approved_ui';
$route['construction/released']                             ='construction/Construction_ctrl/release_ui';  
$route['construction/display_approved']						='construction/Construction_ctrl/get_approved_list';
$route['construction/release']          					='construction/Construction_ctrl/set_release'; 
$route['construction/print']            					='construction/Construction_ctrl/get_order_data';

//===================================================================================================================

/* --------------------------------hr---------------------------------------------------*/

$route['hr/upload_released']                                = 'hr/Hr_ctrl/upload_released_ui';
$route['hr/open_textfile']                                  = 'hr/Hr_ctrl/open_textfile'; 
$route['hr/display_table']                                  = 'hr/Hr_ctrl/display_table';
$route['hr/view_details']                                   = 'hr/Hr_ctrl/view_details'; 
$route['hr/populate_cutoff']                                = 'hr/Hr_ctrl/populate_cut_off_date';
$route['hr/generate_summary']                               = 'hr/Hr_ctrl/generate_summary';
$route['hr/amortization']                                   = 'hr/Hr_ctrl/amortization';
$route['hr/display_amort_table']                            = 'hr/Hr_ctrl/display_amort_table';
$route['hr/display_amortization_table']                     = 'hr/Hr_ctrl/display_amortization_table';
$route['hr/get_company_list']                               = 'hr/Hr_ctrl/get_cc_group_option';
$route['hr/get_comp_list']                                  = 'hr/Hr_ctrl/get_get_company_list';
$route['hr/get_pcc_name']                                   = 'hr/Hr_ctrl/get_pcc_option';
$route['hr/get_depart_name']                                = 'hr/Hr_ctrl/get_depart_name';
$route['hr/get_bu_list']                                    = 'hr/Hr_ctrl/get_bu_list'; 
$route['hr/get_deductiondate']                              = 'hr/Hr_ctrl/get_deductiondate';
$route['hr/submit_amort']                                   = 'hr/Hr_ctrl/submit_amort';
/*--------------------------------end of hr---------------------------------------------*/


/*--------------------------------acctg--------------------------------------------------*/
$route['acctg/ledger']                                      = 'acctg/Acctg_ctrl/display_ledger';
$route['acctg/display_ledger_table']						= 'acctg/Acctg_ctrl/display_ledger_table';		
$route['acctg/amort_schedule']                              = 'acctg/Acctg_ctrl/amort_schedule';
$route['acctg/update_amount']                               = 'acctg/Acctg_ctrl/update_amount';
$route['acctg/set_confirm_amort']                           = 'acctg/Acctg_ctrl/set_confirm_amort'; 
$route['acctg/cash_payment_ui']                             = 'acctg/Acctg_ctrl/cash_payment_ui';
$route['acctg/display_ledger_table_acctg_head']             = 'acctg/Acctg_ctrl/display_ledger_table_acctg_head';
$route['acctg/approve_amount_adjustment']                   = 'acctg/Acctg_ctrl/approve_amount_adjustment';
$route['acctg/Disapprove_amount_adjustment']                = 'acctg/Acctg_ctrl/Disapprove_amount_adjustment';
$route['acctg/generate_summary_acctg']                      = 'acctg/Acctg_ctrl/generate_summary_acctg';  
$route['acctg/view_remarks']                                = 'acctg/Acctg_ctrl/view_remarks';    
$route['acctg/populate_cutoff']                             = 'acctg/Acctg_ctrl/populate_cutoff'; 
$route['acctg/find_unconfirmed']                            = 'acctg/Acctg_ctrl/find_unconfirmed';
$route['acctg/deduction_posting']                           = 'acctg/Acctg_ctrl/deduction_posting';
$route['acctg/display_deduction_posting_table']             = 'acctg/Acctg_ctrl/display_deduction_posting_table';
$route['acctg/display_csv_content']                         = 'acctg/Acctg_ctrl/display_csv_content';
$route['acctg/post_deduction']                              = 'acctg/Acctg_ctrl/post_deduction';
$route['acctg/populate_posting_cutoff']                     = 'acctg/Acctg_ctrl/populate_posting_cutoff';  
$route['acctg/credit_details']                              = 'acctg/Acctg_ctrl/credit_details';
$route['acctg/check_ledger_table']                          = 'acctg/Acctg_ctrl/check_ledger_table';
$route['acctg/check_bu']                                    = 'acctg/Acctg_ctrl/check_bu';
$route['acctg/check_comp']                                  = 'acctg/Acctg_ctrl/check_comp';
$route['acctg/search_emp']                                  = 'acctg/Acctg_ctrl/search_emp';
$route['acctg/emp_details']                                 = 'acctg/Acctg_ctrl/emp_details';

$route['acctg/deduction_posting_NESCO']                     = 'acctg/Acctg_ctrl/deduction_posting_NESCO';
$route['acctg/display_deduction_posting_table_NESCO_NICO']  = 'acctg/Acctg_ctrl/display_deduction_posting_table_NESCO_NICO';
$route['acctg/populate_posting_cutoff_NESCO_NICO']          = 'acctg/Acctg_ctrl/populate_posting_cutoff_NESCO_NICO';  
$route['acctg/post_deduction_nesco']                        = 'acctg/Acctg_ctrl/post_deduction_nesco';  

$route['acctg/deduction_posting_NICO']                     = 'acctg/Acctg_ctrl/deduction_posting_NICO';  
 /*----------------------------------------------------------------------------------------*/

/*-------------------------------aud -------------------------------------------------------*/
$route['aud/aud_body']                                      = 'aud/Aud_ctrl/aud_body'; 
$route['aud/display_aud_table']                             = 'aud/Aud_ctrl/display_aud_table'; 
$route['aud/submit_for_audit']                              = 'aud/Aud_ctrl/submit_for_audit';
/*--------------------------------------------------------------------------------------------*/

/*----------------------------- employee side ------------------------------------------------*/
$route['emp/emp_ui']                                        = 'emp/Emp_ctrl/emp_ui';
$route['emp/get_amort_details']                             = 'emp/Emp_ctrl/get_amort_details';
$route['emp/get_terms_equivalent']                          = 'emp/Emp_ctrl/get_terms_equivalent';
$route['emp/adjust_terms']                                  = 'emp/Emp_ctrl/adjust_terms';
/*--------------------------------------------------------------------------------------------*/

/*-----------------------------   supervisor  ------------------------------------------------*/
$route['superv/supervisor']                                 = 'superv/Supervisor_ctrl/superv_ui';    
$route['superv/display_term_adjustment_requests_list']      = 'superv/Supervisor_ctrl/display_term_adjustment_requests_list';
$route['superv/approve_term']                               = 'superv/Supervisor_ctrl/approve_term';
$route['superv/Disapprove_term']                            = 'superv/Supervisor_ctrl/Disapprove_term'; 

/*--------------------------------------------------------------------------------------------*/


//=============================== HRD APPROVAL ======================================================================
$route['hrd/dashboard'] 									= 'hrd/ApprovalController/dashboard_ui';

$route['hrd/approve_applicant'] 							= 'hrd/ApprovalController/applicants_ui';
$route['hrd/approve_applicant_table'] 						= 'hrd/ApprovalController/applicants_table';
$route['hrd/applicant_credit_details'] 						= 'hrd/ApprovalController/applicant_credit_details';
$route['hrd/process_approve'] 								= 'hrd/ApprovalController/process_approve';
$route['hrd/process_disapprove'] 							= 'hrd/ApprovalController/process_disapprove';

$route['hrd/masterfile'] 									= 'hrd/MasterfileController/masterfile_ui';
$route['hrd/masterfile_table'] 								= 'hrd/MasterfileController/masterfile_table';
$route['hrd/masterfile_credit_details'] 					= 'hrd/MasterfileController/masterfile_credit_details';
$route['hrd/process_cancelEmp'] 							= 'hrd/MasterfileController/process_cancelEmp';

$route['hrd/reports'] 										= 'hrd/ReportsController/reports_ui';
$route['hrd/load_reports_credit_lists'] 					= 'hrd/ReportsController/load_reports_credit_lists';
$route['hrd/generate_credit_lists_pdf'] 					= 'hrd/ReportsController/generate_credit_lists_pdf';
//====================================================================================================================
