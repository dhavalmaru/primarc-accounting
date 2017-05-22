<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use app\models\UserRole;

class UserroleController extends Controller
{
    public function actionIndex(){
        $roleid=$this->session->userdata('role_id');
        if(isset($roleid)){
            $query=$this->db->query("SELECT * FROM user_role_options WHERE section = 'UserRoles' AND role_id='$roleid' AND r_view = 1");
            $result=$query->result();
            if(count($result)>0) {
                $query=$this->db->query("SELECT * FROM user_role_options");
                $result=$query->result();
                if(count($result)>0) {
                    $result[0]->r_export=1;
                    $data['access']=$result;
                }
                
                $gid=$this->session->userdata('groupid');
                $sql = "SELECT A.*, concat_ws(' ',B.c_name,B.c_last_name) as created_by From 
                        (SELECT * FROM user_role_master where g_id = '$gid' or g_id=0) A 
                        LEFT JOIN 
                        ((SELECT '0' as c_id, 'Software' as c_name, 'Developer' as c_last_name FROM contact_master limit 1) 
                         union all 
                         (SELECT c_id, c_name, c_last_name FROM contact_master)) B 
                        ON A.create_by = B.c_id";
                $query=$this->db->query($sql);
                $result=$query->result();
                $data['user']=$result;

                // $query=$this->db->query("SELECT * FROM user_role_master WHERE txn_status ='Approved'");
                // $result=$query->result();
                // $data['approved']=$result;

                // $query=$this->db->query("SELECT * FROM user_role_master WHERE txn_status ='Pending'");
                // $result=$query->result();
                // $data['pending']=$result;

                // $query=$this->db->query("SELECT * FROM user_role_master WHERE txn_status ='Rejected'");
                // $result=$query->result();
                // $data['rejected']=$result;

                // $query=$this->db->query("SELECT * FROM user_role_master WHERE txn_status ='In Process'");
                // $result=$query->result();
                // $data['inprocess']=$result;

                load_view('management/user_role_list', $data);
            } else {
                echo '<script>alert("You donot have access to this page.");</script>';
                $this->load->view('login/main_page');
            }
        } else {
            echo '<script>alert("You donot have access to this page.");</script>';
            $this->load->view('login/main_page');
        }
    }

    public function get_report_groups(){
        $gid=$this->session->userdata('groupid');
        $query=$this->db->query("SELECT * FROM report_groups where rep_grp_id = '$gid'");
        $result=$query->result();

        if (count($result)>0) {
            for($i=0; $i<count($result); $i++) {
                $data['rep_' . $result[$i]->rep_id . '_view'] = $result[$i]->rep_view;
            }
        }

        $data['rep_grp_1']=0;
        $data['rep_grp_2']=0;
        $data['rep_grp_3']=0;
        // $data['rep_grp_4']=0;
        // $data['rep_grp_5']=0;
        // $data['rep_grp_6']=0;

        if (isset($data['rep_1_view'])) {if ($data['rep_1_view']==1) $data['rep_grp_1']=1;}
        if (isset($data['rep_2_view'])) {if ($data['rep_2_view']==1) $data['rep_grp_1']=1;}
        if (isset($data['rep_3_view'])) {if ($data['rep_3_view']==1) $data['rep_grp_1']=1;}
        if (isset($data['rep_4_view'])) {if ($data['rep_4_view']==1) $data['rep_grp_1']=1;}
        if (isset($data['rep_5_view'])) {if ($data['rep_5_view']==1) $data['rep_grp_1']=1;}
        if (isset($data['rep_6_view'])) {if ($data['rep_6_view']==1) $data['rep_grp_1']=1;}
        if (isset($data['rep_19_view'])) {if ($data['rep_19_view']==1) $data['rep_grp_1']=1;}

        if (isset($data['rep_7_view'])) {if ($data['rep_7_view']==1) $data['rep_grp_2']=1;}
        if (isset($data['rep_8_view'])) {if ($data['rep_8_view']==1) $data['rep_grp_2']=1;}
        if (isset($data['rep_9_view'])) {if ($data['rep_9_view']==1) $data['rep_grp_2']=1;}
        if (isset($data['rep_10_view'])) {if ($data['rep_10_view']==1) $data['rep_grp_2']=1;}
        if (isset($data['rep_20_view'])) {if ($data['rep_20_view']==1) $data['rep_grp_2']=1;}

        if (isset($data['rep_11_view'])) {if ($data['rep_11_view']==1) $data['rep_grp_3']=1;}
        if (isset($data['rep_12_view'])) {if ($data['rep_12_view']==1) $data['rep_grp_3']=1;}
        if (isset($data['rep_13_view'])) {if ($data['rep_13_view']==1) $data['rep_grp_3']=1;}
        if (isset($data['rep_14_view'])) {if ($data['rep_14_view']==1) $data['rep_grp_3']=1;}
        if (isset($data['rep_15_view'])) {if ($data['rep_15_view']==1) $data['rep_grp_3']=1;}
        if (isset($data['rep_16_view'])) {if ($data['rep_16_view']==1) $data['rep_grp_3']=1;}
        if (isset($data['rep_17_view'])) {if ($data['rep_17_view']==1) $data['rep_grp_3']=1;}
        if (isset($data['rep_18_view'])) {if ($data['rep_18_view']==1) $data['rep_grp_3']=1;}

        // if (isset($data['rep_17_view'])) {if ($data['rep_17_view']==1) $data['rep_grp_4']=1;}
        // if (isset($data['rep_18_view'])) {if ($data['rep_18_view']==1) $data['rep_grp_4']=1;}
        // if (isset($data['rep_19_view'])) {if ($data['rep_19_view']==1) $data['rep_grp_4']=1;}
        // if (isset($data['rep_20_view'])) {if ($data['rep_20_view']==1) $data['rep_grp_4']=1;}

        // if (isset($data['rep_21_view'])) {if ($data['rep_21_view']==1) $data['rep_grp_5']=1;}
        // if (isset($data['rep_22_view'])) {if ($data['rep_22_view']==1) $data['rep_grp_5']=1;}
        // if (isset($data['rep_23_view'])) {if ($data['rep_23_view']==1) $data['rep_grp_5']=1;}
        // if (isset($data['rep_24_view'])) {if ($data['rep_24_view']==1) $data['rep_grp_5']=1;}
        // if (isset($data['rep_25_view'])) {if ($data['rep_25_view']==1) $data['rep_grp_5']=1;}

        // if (isset($data['rep_26_view'])) {if ($data['rep_26_view']==1) $data['rep_grp_6']=1;}
        // if (isset($data['rep_27_view'])) {if ($data['rep_27_view']==1) $data['rep_grp_6']=1;}
        // if (isset($data['rep_28_view'])) {if ($data['rep_28_view']==1) $data['rep_grp_6']=1;}
        // if (isset($data['rep_29_view'])) {if ($data['rep_29_view']==1) $data['rep_grp_6']=1;}
        // if (isset($data['rep_30_view'])) {if ($data['rep_30_view']==1) $data['rep_grp_6']=1;}

        return $data;
    }

    public function addnew(){
        $gid=$this->session->userdata('groupid');
        $roleid=$this->session->userdata('role_id');
        if(isset($roleid)){
            $query=$this->db->query("SELECT * FROM user_role_options WHERE section = 'UserRoles' AND role_id='$roleid' AND r_insert = 1");
            $result=$query->result();
            if(count($result)>0) {
                $data = $this->get_report_groups();
                load_view('management/user_role_details',$data);
            } else {
                echo '<script>alert("You donot have access to this page.");</script>';
                $this->load->view('login/main_page');
            }
        } else {
            echo '<script>alert("You donot have access to this page.");</script>';
            $this->load->view('login/main_page');
        }
    }

    public function saverecord(){
        $gid=$this->session->userdata('groupid');
        $roleid=$this->session->userdata('role_id');
        if(isset($roleid)){
            $query=$this->db->query("SELECT * FROM user_role_options WHERE section = 'UserRoles' AND role_id='$roleid' AND r_insert = 1");
            $result=$query->result();
            if(count($result)>0) {
                $now=date('Y-m-d H:i:s');
                $modnow=date('Y-m-d H:i:s');
                $curusr=$this->session->userdata('session_id');
                $sects = array('Contacts', 'Bank', 'Owner', 'Purchase', 'Allocation', 'Sale', 'Rent', 'BankEntry', 'Loan', 'Expense', 'Maintenance', 'Valuation', 'Tax', 'Reports');
                $vw=$this->input->post('view[]');
                $ins=$this->input->post('insert[]');
                $upd=$this->input->post('update[]');
                $del=$this->input->post('delete[]');
                $apps=$this->input->post('approval[]');
                $exp=$this->input->post('export[]');

                print_r($vw);
                $data = array(
                    'role_name' => $this->input->post('role'),
                    'r_status' => 'Active',
                    'r_description' => $this->input->post('role_description'),
                    'txn_status' => 'Approved',
                    'create_by' => $curusr,
                    'create_date' => $now,
                    'modified_by' => $curusr,
                    'modified_date' => $modnow,
                    'g_id' => $gid,
                );

                $this->db->insert('user_role_master', $data);
                $rid=$this->db->insert_id();

                $logarray['table_id']=$this->db->insert_id();
                $logarray['module_name']='User Assign';
                $logarray['cnt_name']='Manage';
                $logarray['action']='User Assign Record Inserted';
                $logarray['gp_id']=$gid;
                $this->user_access_log_model->insertAccessLog($logarray);

                $a=$b=$c=$d=$e=$f=0;
                $viw=$insrt=$updt=$delt=$apprs=$expt=NULL;

                for ($i=0; $i < count($sects) ; $i++) { 
                    echo $i." Count <br>";
                    echo $a." A<br>";
                    echo $b." B<br>";
                    echo $c." C<br>";
                    echo $d." D<br>";
                    echo $e." E<br>";
                    echo $f." F<br>";
                    print_r($vw);
                    if(count($vw)>$a){
                        if($vw[$a]==$i){
                            $viw[$i]=1;
                            if(count($viw)>$a){
                                $a++;
                            }
                        } else {
                            $viw[$i]=0;
                        }    
                    } else {
                        $viw[$i]=0;
                    }
                    

                    if(count($ins)>$b) {
                        if($ins[$b]==$i){
                            $insrt[$i]=1;
                            if(count($insrt)>$b){
                                $b++;
                            }
                        } else {
                            $insrt[$i]=0;
                        }
                    } else {
                        $insrt[$i]=0;
                    }

                    if(count($upd)>$c) {
                        if($upd[$c]==$i){
                            echo $i;
                            echo $upd[$c];
                            $updt[$i]=1;
                                echo count($updt);  echo $c;
                             if(count($updt)>$c){
                                $c++;
                             
                            }
                        } else {
                            $updt[$i]=0;
                        }
                    } else {
                        $updt[$i]=0;
                    }
                   
                    if(count($del)>$d) {
                        if($del[$d]==$i){
                            $delt[$i]=1;
                            if(count($delt)>$d){
                                $d++;
                            }
                        } else {
                            $delt[$i]=0;
                        }
                    } else {
                        $delt[$i]=0;
                    }

                    if(count($apps)>$e) {
                        if($apps[$e]==$i){
                            $apprs[$i]=1;
                            if(count($apprs)>$e){
                                $e++;
                            }
                        } else {
                            $apprs[$i]=0;
                        }
                    } else {
                        $apprs[$i]=0;
                    }

                    if(count($exp)>$f) {
                        if($exp[$f]==$i){
                            $expt[$i]=1;
                            if(count($expt)>$f){
                                $f++;
                            }
                        } else {
                            $expt[$i]=0;
                        }
                    } else {
                        $expt[$i]=0;
                    }

                }

                for ($i=0; $i < count($sects) ; $i++) { 

                    $data = array(
                        'role_id' => $rid,
                        'section' => $sects[$i],
                        'r_view' => $viw[$i],
                        'r_insert' => $insrt[$i],
                        'r_edit' => $updt[$i],
                        'r_delete' => $delt[$i],
                        'r_approvals' => $apprs[$i],
                        'r_export' => $expt[$i]
                     );
                    $this->db->insert('user_role_options', $data);
                }

                $group1=$this->input->post('group1[]');
                $group2=$this->input->post('group2[]');
                $group3=$this->input->post('group3[]');
                // $group4=$this->input->post('group4[]');
                // $group5=$this->input->post('group5[]');
                // $group6=$this->input->post('group6[]');

                for ($i=0; $i < count($group1) ; $i++) {
                    $rep_id = $group1[$i];

                    $query=$this->db->query("SELECT * FROM report_roles WHERE rep_id = '$rep_id' and role_id = '$rid'");
                    $result=$query->result();
                    if (count($result)>0) {
                        $this->db->query("update report_roles set rep_view = '1', modified_by='$curusr', modified_date='$modnow' WHERE rep_id = '$rep_id' and role_id = '$rid'");
                    } else {
                        $this->db->query("insert into report_roles (rep_id, role_id, rep_view, created_by, created_date) values ('$rep_id','$rid','1','$curusr','$now')");
                    }
                }

                redirect(base_url().'index.php/Manage');
            } else {
                echo '<script>alert("You donot have access to this page.");</script>';
                $this->load->view('login/main_page');
            }
        } else {
            echo '<script>alert("You donot have access to this page.");</script>';
            $this->load->view('login/main_page');
        }
    }

    public function view($rid){
        $gid=$this->session->userdata('groupid');
        $roleid=$this->session->userdata('role_id');
        if(isset($roleid)){
            $query=$this->db->query("SELECT * FROM user_role_options WHERE section = 'UserRoles' AND role_id='$roleid' AND r_view = 1");
            $result=$query->result();
            if(count($result)>0) {
                $data = $this->get_report_groups();

                $query=$this->db->query("SELECT * FROM user_role_master WHERE rl_id = '$rid'");
                $result=$query->result();
                $data['edituser']=$result;

                $query=$this->db->query("SELECT * FROM user_role_options WHERE role_id = '$rid' and section in('Contacts', 'Bank', 'Owner',
                                        'Purchase', 'Allocation', 'Sale', 'Rent', 'BankEntry', 'Loan', 'Expense', 'Maintenance', 'Valuation', 'Tax', 'Reports') order by op_id");
                $result=$query->result();
                $data['editoptions']=array();

                if (count($result)>0) {
                    for($i=0;$i<count($result);$i++) {
                        if ($result[$i]->section=="Contacts") $num=0;
                        else if ($result[$i]->section=="Bank") $num=1;
                        else if ($result[$i]->section=="Owner") $num=2;
                        else if ($result[$i]->section=="Purchase") $num=3;
                        else if ($result[$i]->section=="Allocation") $num=4;
                        else if ($result[$i]->section=="Sale") $num=5;
                        else if ($result[$i]->section=="Rent") $num=6;
                        else if ($result[$i]->section=="BankEntry") $num=7;
                        else if ($result[$i]->section=="Loan") $num=8;
                        else if ($result[$i]->section=="Expense") $num=9;
                        else if ($result[$i]->section=="Maintenance") $num=10;
                        else if ($result[$i]->section=="Valuation") $num=11;
                        else if ($result[$i]->section=="Tax") $num=12;
                        else if ($result[$i]->section=="Reports") $num=13;

                        $data['editoptions'][$num]=$result[$i];
                    }
                }

                $data['r_id']=$rid;

                $query=$this->db->query("SELECT * FROM report_roles WHERE role_id = '$rid'");
                $result=$query->result();
                if (count($result)>0) {
                    for($i=0;$i<count($result);$i++) {
                        $data['rep_' . $result[$i]->rep_id]=$result[$i]->rep_view;
                    }
                }

                load_view('management/user_role_view',$data);
            } else {
                echo '<script>alert("You donot have access to this page.");</script>';
                $this->load->view('login/main_page');
            }
        } else {
            echo '<script>alert("You donot have access to this page.");</script>';
            $this->load->view('login/main_page');
        }
    }
    
    public function edit($rid){
        $gid=$this->session->userdata('groupid');
        $roleid=$this->session->userdata('role_id');
        if(isset($roleid)){
            $query=$this->db->query("SELECT * FROM user_role_options WHERE section = 'UserRoles' AND role_id='$roleid' AND r_edit = 1");
            $result=$query->result();
            if(count($result)>0) {
                $data = $this->get_report_groups();

                $query=$this->db->query("SELECT * FROM user_role_master WHERE rl_id = '$rid'");
                $result=$query->result();
                $data['edituser']=$result;

                $query=$this->db->query("SELECT * FROM user_role_options WHERE role_id = '$rid' and section in('Contacts', 'Bank', 'Owner',
                                        'Purchase', 'Allocation', 'Sale', 'Rent', 'BankEntry', 'Loan', 'Expense', 'Maintenance', 'Valuation', 'Tax', 'Reports') order by op_id");
                $result=$query->result();
                $data['editoptions']=array();

                if (count($result)>0) {
                    for($i=0;$i<count($result);$i++) {
                        if ($result[$i]->section=="Contacts") $num=0;
                        else if ($result[$i]->section=="Bank") $num=1;
                        else if ($result[$i]->section=="Owner") $num=2;
                        else if ($result[$i]->section=="Purchase") $num=3;
                        else if ($result[$i]->section=="Allocation") $num=4;
                        else if ($result[$i]->section=="Sale") $num=5;
                        else if ($result[$i]->section=="Rent") $num=6;
                        else if ($result[$i]->section=="BankEntry") $num=7;
                        else if ($result[$i]->section=="Loan") $num=8;
                        else if ($result[$i]->section=="Expense") $num=9;
                        else if ($result[$i]->section=="Maintenance") $num=10;
                        else if ($result[$i]->section=="Valuation") $num=11;
                        else if ($result[$i]->section=="Tax") $num=12;
                        else if ($result[$i]->section=="Reports") $num=13;


                        $data['editoptions'][$num]=$result[$i];
                    }
                }

                $data['r_id']=$rid;

                $query=$this->db->query("SELECT * FROM report_roles WHERE role_id = '$rid'");
                $result=$query->result();
                if (count($result)>0) {
                    for($i=0;$i<count($result);$i++) {
                        $data['rep_' . $result[$i]->rep_id]=$result[$i]->rep_view;
                    }
                }


                load_view('management/user_role_details',$data);
            } else {
                echo '<script>alert("You donot have access to this page.");</script>';
                $this->load->view('login/main_page');
            }
        } else {
            echo '<script>alert("You donot have access to this page.");</script>';
            $this->load->view('login/main_page');
        }
    }
    
    public function copy($rid){
        $gid=$this->session->userdata('groupid');
        $roleid=$this->session->userdata('role_id');
        if(isset($roleid)){
            $query=$this->db->query("SELECT * FROM user_role_options WHERE section = 'UserRoles' AND role_id='$roleid' AND r_edit = 1");
            $result=$query->result();
            if(count($result)>0) {
                $data = $this->get_report_groups();

                $query=$this->db->query("SELECT * FROM user_role_master WHERE rl_id = '$rid'");
                $result=$query->result();
                $data['edituser']=$result;

                $query=$this->db->query("SELECT * FROM user_role_options WHERE role_id = '$rid' and section in('Contacts', 'Bank', 'Owner',
                                        'Purchase', 'Allocation', 'Sale', 'Rent', 'BankEntry', 'Loan', 'Expense', 'Maintenance', 'Valuation', 'Tax', 'Reports') order by op_id");
                $result=$query->result();
                $data['editoptions']=$result;

                $data['r_id']=0;

                $query=$this->db->query("SELECT * FROM report_roles WHERE role_id = '$rid'");
                $result=$query->result();
                if (count($result)>0) {
                    for($i=0;$i<count($result);$i++) {
                        $data['rep_' . $result[$i]->rep_id]=$result[$i]->rep_view;
                    }
                }

                load_view('management/user_role_details',$data);
            } else {
                echo '<script>alert("You donot have access to this page.");</script>';
                $this->load->view('login/main_page');
            }
        } else {
            echo '<script>alert("You donot have access to this page.");</script>';
            $this->load->view('login/main_page');
        }
    }
    
    public function updaterecord($rid){
        $gid=$this->session->userdata('groupid');
        $roleid=$this->session->userdata('role_id');
        if(isset($roleid)){
            $query=$this->db->query("SELECT * FROM user_role_options WHERE section = 'UserRoles' AND role_id='$roleid' AND r_edit = 1");
            $result=$query->result();
            if(count($result)>0) {
                $now=date('Y-m-d H:i:s');
                $modnow=date('Y-m-d H:i:s');
                $curusr=$this->session->userdata('session_id');

                $sects = array('Contacts', 'Bank', 'Owner', 'Purchase', 'Allocation', 'Sale', 'Rent', 'BankEntry', 'Loan', 'Expense', 'Maintenance', 'Valuation', 'Tax', 'Reports');
                $vw=$this->input->post('view[]');
                $ins=$this->input->post('insert[]');
                $upd=$this->input->post('update[]');
                $del=$this->input->post('delete[]');
                $apps=$this->input->post('approval[]');
                $exp=$this->input->post('export[]');
                
                $data = array(
                    'role_name' => $this->input->post('role'),
                    'r_status' => 'Active',
                    'r_description' => $this->input->post('role_description'),
                    'modified_by' => $curusr,
                    'modified_date' => $modnow,
                    'g_id' => $gid,
                 );
                $this->db->where('rl_id', $rid);
                $this->db->update('user_role_master', $data);
                
                $logarray['table_id']=$rid;
                $logarray['module_name']='User Assign';
                $logarray['cnt_name']='Manage';
                $logarray['action']='User Assign Record Updated';
                $logarray['gp_id']=$gid;
                $this->user_access_log_model->insertAccessLog($logarray);

                $a=$b=$c=$d=$e=$f=0;
                $viw=$insrt=$updt=$delt=$apprs=$expt=NULL;
                for ($i=0; $i < count($sects) ; $i++) { 
                    if(count($vw)>$a){
                        if($vw[$a]==$i){
                            $viw[$i]=1;
                            if(count($viw)>$a){
                                $a++;
                            }
                        } else {
                            $viw[$i]=0;
                        }    
                    } else {
                        $viw[$i]=0;
                    }
                    

                    if(count($ins)>$b) {
                        if($ins[$b]==$i){
                            $insrt[$i]=1;
                            if(count($insrt)>$b){
                                $b++;
                            }
                        } else {
                            $insrt[$i]=0;
                        }
                    } else {
                        $insrt[$i]=0;
                    }

                    if(count($upd)>$c) {
                        if($upd[$c]==$i){
                            echo $i;
                            echo $upd[$c];
                            $updt[$i]=1;
                                echo count($updt);  echo $c;
                             if(count($updt)>$c){
                                $c++;
                             
                            }
                        } else {
                            $updt[$i]=0;
                        }
                    } else {
                        $updt[$i]=0;
                    }
                   
                    if(count($del)>$d) {
                        if($del[$d]==$i){
                            $delt[$i]=1;
                            if(count($delt)>$d){
                                $d++;
                            }
                        } else {
                            $delt[$i]=0;
                        }
                    } else {
                        $delt[$i]=0;
                    }

                    if(count($apps)>$e) {
                        if($apps[$e]==$i){
                            $apprs[$i]=1;
                            if(count($apprs)>$e){
                                $e++;
                            }
                        } else {
                            $apprs[$i]=0;
                        }
                    } else {
                        $apprs[$i]=0;
                    }

                    if(count($exp)>$f) {
                        if($exp[$f]==$i){
                            $expt[$i]=1;
                            if(count($expt)>$f){
                                $f++;
                            }
                        } else {
                            $expt[$i]=0;
                        }
                    } else {
                        $expt[$i]=0;
                    }
                }

                $this->db->where('role_id', $rid);
                $this->db->delete('user_role_options');

                for ($i=0; $i < count($sects) ; $i++) { 

                    $data = array(
                        'role_id' => $rid,
                        'section' => $sects[$i],
                        'r_view' => $viw[$i],
                        'r_insert' => $insrt[$i],
                        'r_edit' => $updt[$i],
                        'r_delete' => $delt[$i],
                        'r_approvals' => $apprs[$i],
                        'r_export' => $expt[$i]

                     );
                    $this->db->insert('user_role_options', $data);
                }

                if($rid==1) {
                    $data = array(
                        'role_id' => $rid,
                        'section' => 'User',
                        'r_view' => 1,
                        'r_insert' => 1,
                        'r_edit' => 1,
                        'r_delete' => 1,
                        'r_approvals' => 1,
                        'r_export' => 1
                    );
                    $this->db->insert('user_role_options', $data);

                    $data = array(
                        'role_id' => $rid,
                        'section' => 'UserRoles',
                        'r_view' => 1,
                        'r_insert' => 1,
                        'r_edit' => 1,
                        'r_delete' => 1,
                        'r_approvals' => 1,
                        'r_export' => 1
                    );
                    $this->db->insert('user_role_options', $data);
                }

                // $group1=$this->input->post('group1[]');
                // $group2=$this->input->post('group2[]');
                // $group3=$this->input->post('group3[]');
                // $group4=$this->input->post('group4[]');
                // $group5=$this->input->post('group5[]');
                // $group6=$this->input->post('group6[]');

                $this->db->query("update report_roles set rep_view = '0', modified_by='$curusr', modified_date='$modnow' WHERE role_id = '$rid'");

                $report=$this->input->post('report[]');

                // echo '<script>console.log("' . count($report) . '");</script>';
                for ($i=0; $i < count($report) ; $i++) {
                    $rep_id = $report[$i];

                    $query=$this->db->query("SELECT * FROM report_roles WHERE rep_id = '$rep_id' and role_id = '$rid'");
                    $result=$query->result();
                    if (count($result)>0) {
                        $this->db->query("update report_roles set rep_view = '1', modified_by='$curusr', modified_date='$modnow' WHERE rep_id = '$rep_id' and role_id = '$rid'");
                    } else {
                        $this->db->query("insert into report_roles (rep_id, role_id, rep_view, created_by, created_date) values ('$rep_id','$rid','1','$curusr','$now')");
                    }
                }

                redirect(base_url().'index.php/Manage');
            } else {
                echo '<script>alert("You donot have access to this page.");</script>';
                $this->load->view('login/main_page');
            }
        } else {
            echo '<script>alert("You donot have access to this page.");</script>';
            $this->load->view('login/main_page');
        }
    }

    function checkRoleAvailability() {
        $rl_id = html_escape($this->input->post('rl_id'));
        $role_name = html_escape($this->input->post('role_name'));
        $gid=$this->session->userdata('groupid');

        $query = $this->db->query("SELECT * FROM user_role_master WHERE rl_id != '$rl_id' AND role_name = '$role_name' AND g_id = '$gid'");
        if($query->num_rows()!=0){
            echo 1;
        } else {
            echo 0;
        }
    }

}
?>