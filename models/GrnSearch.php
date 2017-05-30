<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\SqlDataProvider;
use yii\data\Pagination;
use app\models\Grn;

/**
 * GrnSearch represents the model behind the search form about `app\models\Grn`.
 */
class GrnSearch extends Grn
{
    /* your calculated attribute */
    public $invoiceNo;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['grn_id', 'vendor_id', 'invoice_sku', 'scanned_sku', 'category_id', 'invoice_qty', 'scanned_qty', 'company_id', 'user_id', 'summary_user_id', 'manufacturer_id', 'received_sku', 'received_qty', 'shortage', 'is_active', 'shipment_id', 'no_of_sku', 'sku', 'qty', 'no_of_boxes', 'warehouse_bal', 'goods_not_shipped', 'unresolved_unit', 'sellable_units', 'damaged_units', 'expired_units', 'sellable_amt', 'unsellable_amt', 'asin_issue_units', 'listing_issue_units', 'hazmat_issue_units', 'mrp_issue_units', 'marketplace_removed_units', 'excess_units', 'total_issues', 'created_by', 'updated_by'], 'integer'],
            [['gi_id', 'gi_date', 'vat_cst', 'grn_start_date_time', 'vendor_name', 'customer_name', 'customer_type', 'category_name', 'grn_no', 'location', 'po_no', 'warehouse_id', 'idt_warehouse_code', 'user_name', 'summary_user_name', 'gi_type', 'manufacturer', 'status', 'remarks', 'summary_status', 'summary_docket', 'shipment_date', 'vehicle_no', 'delivery_challan_no', 'grn_end_date_time', 'created_date', 'updated_date', 'approver_comments', 'approver_user_id', 'approver_name', 'grn_approved_date_time', 'from_warehouse_name', 'interdepot_type', 'removal_id', 'challan_no', 'from_warehouse_code', 'is_bulk_upload', 'combo_type', 'combo_child_sku', 'invoiceNo'], 'safe'],
            [['invoice_amt', 'invoice_val_bef_tax', 'invoice_val_after_tax', 'payable_val_before_tax', 'payable_val_after_tax', 'percent_fill_rate'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        // $query = Grn::find()->where("status='approved' and grn_id not in (select distinct grn_id from acc_grn_entries)");

        // $query = Grn::find()->all();

        // echo $query[0]->grn_id;

        // print_r( $query[0] );


        $query = Grn::find()->where("grn.status='approved' and acc_grn_entries.grn_id is null")
                            ->leftJoin('acc_grn_entries', 'grn.grn_id=acc_grn_entries.grn_id')
                            ->select("grn.gi_id, grn.location, grn.vendor_name, grn.scanned_qty, grn.payable_val_after_tax, 
                                grn.gi_date, grn.status")
                            ->orderBy('grn.grn_id');

        // $query = Grn::find()->where("grn.status='approved' and acc_grn_entries_gi_id is null")
        //                     ->leftJoin('acc_grn_entries', 'grn.grn_id=acc_grn_entries.grn_id')
        //                     ->select("grn.gi_id, grn.location, grn.vendor_name, grn.scanned_qty, grn.payable_val_after_tax, 
        //                             grn.gi_date, grn.status, acc_grn_entries.gi_id as acc_grn_entries_gi_id")
        //                     ->orderBy('grn.gi_id');

        // $query = Grn::find()->where("grn.status='approved'")
        //                     ->leftJoin('acc_grn_entries', 'grn.grn_id=acc_grn_entries.grn_id')
        //                     ->andWhere(['=', 'acc_grn_entries_gi_id', 1])
        //                     ->select("grn.gi_id, grn.location, grn.vendor_name, grn.scanned_qty, grn.payable_val_after_tax, 
        //                             grn.gi_date, grn.status, acc_grn_entries.gi_id as acc_grn_entries_gi_id")
        //                     ->orderBy('grn.gi_id');


        // $sql = "select * from 
        //         (select A.*, B.grn_id as b_grn_id from 
        //         (select * from grn where status = 'approved') A 
        //         left join 
        //         (select distinct grn_id from acc_grn_entries) B 
        //         on (A.grn_id = B.grn_id)) C 
        //         where b_grn_id is null";
        // $query = Grn::findBySql($sql, [':status' => 'approved']);

        // $pagination = new Pagination([
        //     'defaultPageSize' => 20,
        //     'totalCount' => $query->count(),
        // ]);
        // $query = $query->orderBy('grn_id')
        //                 ->offset($pagination->offset)
        //                 ->limit($pagination->limit);

        // $query = Grn::find()->from("select * from grn")->where("status='approved'");


        // $query = Grn::find()->where("grn.status='approved'")
        //                     ->leftJoin('acc_grn_entries', 'grn.grn_id=acc_grn_entries.grn_id')
        //                     ->select("grn.gi_id, grn.location, grn.vendor_name, grn.scanned_qty, grn.payable_val_after_tax, 
        //                             grn.gi_date, grn.status, acc_grn_entries.gi_id as acc_grn_entries_gi_id")
        //                     ->orderBy('grn.gi_id');


        // $sql = "select * from 
        //         (select A.*, B.grn_id as b_grn_id from 
        //         (select * from grn where status = 'approved') A 
        //         left join 
        //         (select distinct grn_id from acc_grn_entries) B 
        //         on (A.grn_id = B.grn_id)) C 
        //         where b_grn_id is null";

        // $query = Grn::findBySql($sql)->all();

        // $model = Yii::$app->db->createCommand($sql);
        // $query = $model->queryAll();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'grn_id' => $this->grn_id,
            'gi_date' => $this->gi_date,
            'grn_start_date_time' => $this->grn_start_date_time,
            'vendor_id' => $this->vendor_id,
            'invoice_sku' => $this->invoice_sku,
            'scanned_sku' => $this->scanned_sku,
            'category_id' => $this->category_id,
            'invoice_qty' => $this->invoice_qty,
            'invoice_amt' => $this->invoice_amt,
            'scanned_qty' => $this->scanned_qty,
            'company_id' => $this->company_id,
            'user_id' => $this->user_id,
            'summary_user_id' => $this->summary_user_id,
            'invoice_val_bef_tax' => $this->invoice_val_bef_tax,
            'invoice_val_after_tax' => $this->invoice_val_after_tax,
            'payable_val_before_tax' => $this->payable_val_before_tax,
            'payable_val_after_tax' => $this->payable_val_after_tax,
            'manufacturer_id' => $this->manufacturer_id,
            'received_sku' => $this->received_sku,
            'received_qty' => $this->received_qty,
            'shortage' => $this->shortage,
            'is_active' => $this->is_active,
            'shipment_date' => $this->shipment_date,
            'shipment_id' => $this->shipment_id,
            'no_of_sku' => $this->no_of_sku,
            'sku' => $this->sku,
            'qty' => $this->qty,
            'no_of_boxes' => $this->no_of_boxes,
            'warehouse_bal' => $this->warehouse_bal,
            'goods_not_shipped' => $this->goods_not_shipped,
            'unresolved_unit' => $this->unresolved_unit,
            'sellable_units' => $this->sellable_units,
            'damaged_units' => $this->damaged_units,
            'expired_units' => $this->expired_units,
            'sellable_amt' => $this->sellable_amt,
            'unsellable_amt' => $this->unsellable_amt,
            'asin_issue_units' => $this->asin_issue_units,
            'listing_issue_units' => $this->listing_issue_units,
            'hazmat_issue_units' => $this->hazmat_issue_units,
            'mrp_issue_units' => $this->mrp_issue_units,
            'marketplace_removed_units' => $this->marketplace_removed_units,
            'excess_units' => $this->excess_units,
            'percent_fill_rate' => $this->percent_fill_rate,
            'total_issues' => $this->total_issues,
            'grn_end_date_time' => $this->grn_end_date_time,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_date' => $this->created_date,
            'updated_date' => $this->updated_date,
            'grn_approved_date_time' => $this->grn_approved_date_time,
        ]);

        $query->andFilterWhere(['like', 'gi_id', $this->gi_id])
            ->andFilterWhere(['like', 'vat_cst', $this->vat_cst])
            ->andFilterWhere(['like', 'vendor_name', $this->vendor_name])
            ->andFilterWhere(['like', 'customer_name', $this->customer_name])
            ->andFilterWhere(['like', 'customer_type', $this->customer_type])
            ->andFilterWhere(['like', 'category_name', $this->category_name])
            ->andFilterWhere(['like', 'grn_no', $this->grn_no])
            ->andFilterWhere(['like', 'location', $this->location])
            ->andFilterWhere(['like', 'po_no', $this->po_no])
            ->andFilterWhere(['like', 'warehouse_id', $this->warehouse_id])
            ->andFilterWhere(['like', 'idt_warehouse_code', $this->idt_warehouse_code])
            ->andFilterWhere(['like', 'user_name', $this->user_name])
            ->andFilterWhere(['like', 'summary_user_name', $this->summary_user_name])
            ->andFilterWhere(['like', 'gi_type', $this->gi_type])
            ->andFilterWhere(['like', 'manufacturer', $this->manufacturer])
            ->andFilterWhere(['like', 'status', $this->status])
            ->andFilterWhere(['like', 'remarks', $this->remarks])
            ->andFilterWhere(['like', 'summary_status', $this->summary_status])
            ->andFilterWhere(['like', 'summary_docket', $this->summary_docket])
            ->andFilterWhere(['like', 'vehicle_no', $this->vehicle_no])
            ->andFilterWhere(['like', 'delivery_challan_no', $this->delivery_challan_no])
            ->andFilterWhere(['like', 'approver_comments', $this->approver_comments])
            ->andFilterWhere(['like', 'approver_user_id', $this->approver_user_id])
            ->andFilterWhere(['like', 'approver_name', $this->approver_name])
            ->andFilterWhere(['like', 'from_warehouse_name', $this->from_warehouse_name])
            ->andFilterWhere(['like', 'interdepot_type', $this->interdepot_type])
            ->andFilterWhere(['like', 'removal_id', $this->removal_id])
            ->andFilterWhere(['like', 'challan_no', $this->challan_no])
            ->andFilterWhere(['like', 'from_warehouse_code', $this->from_warehouse_code])
            ->andFilterWhere(['like', 'is_bulk_upload', $this->is_bulk_upload])
            ->andFilterWhere(['like', 'combo_type', $this->combo_type])
            ->andFilterWhere(['like', 'combo_child_sku', $this->combo_child_sku]);

        return $dataProvider;
    }
}
