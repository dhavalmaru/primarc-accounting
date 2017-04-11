<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\GrnEntries;

/**
 * GrnEntriesSearch represents the model behind the search form about `app\models\GrnEntries`.
 */
class GrnEntriesSearch extends GrnEntries
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['grn_entries_id', 'is_active', 'po_qty', 'proper_qty', 'damaged_qty', 'is_sku_adjustment', 'total_qty', 'shortage_qty', 'excess_qty', 'po_shortage_qty', 'po_excess_qty', 'invoice_qty', 'expiry_qty', 'mrp_issue_qty', 'grn_id', 'company_id', 'min_no_of_months_shelf_life_required', 'created_by', 'updated_by', 'physical_allocation', 'removal_qty', 'challan_qty', 'removal_shortage_qty', 'removal_excess_qty', 'challan_shortage_qty', 'challan_excess_qty', 'is_first_scan', 'initial_invoice_qty', 'demand_qty'], 'integer'],
            [['ean', 'asin', 'fnsku', 'marketplace_title', 'product_title', 'vat_cst', 'msku', 'psku', 'product_type', 'status', 'manufacturing_date', 'expiry_date', 'expiry_issue', 'remarks', 'issues', 'created_date', 'updated_date', 'issue_type', 'other_type', 'invoice_no', 'is_bulk_upload'], 'safe'],
            [['list_price', 'box_price', 'cost_excl_vat', 'vat_percen', 'cost_incl_vat_cst', 'percent_expiry_left', 'expiry_months_left', 'min_percentage_shelf_life_required'], 'number'],
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
        $query = GrnEntries::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'grn_entries_id' => $this->grn_entries_id,
            'is_active' => $this->is_active,
            'list_price' => $this->list_price,
            'box_price' => $this->box_price,
            'cost_excl_vat' => $this->cost_excl_vat,
            'vat_percen' => $this->vat_percen,
            'cost_incl_vat_cst' => $this->cost_incl_vat_cst,
            'po_qty' => $this->po_qty,
            'proper_qty' => $this->proper_qty,
            'damaged_qty' => $this->damaged_qty,
            'is_sku_adjustment' => $this->is_sku_adjustment,
            'total_qty' => $this->total_qty,
            'shortage_qty' => $this->shortage_qty,
            'excess_qty' => $this->excess_qty,
            'po_shortage_qty' => $this->po_shortage_qty,
            'po_excess_qty' => $this->po_excess_qty,
            'invoice_qty' => $this->invoice_qty,
            'manufacturing_date' => $this->manufacturing_date,
            'expiry_date' => $this->expiry_date,
            'expiry_qty' => $this->expiry_qty,
            'mrp_issue_qty' => $this->mrp_issue_qty,
            'percent_expiry_left' => $this->percent_expiry_left,
            'expiry_months_left' => $this->expiry_months_left,
            'grn_id' => $this->grn_id,
            'company_id' => $this->company_id,
            'min_percentage_shelf_life_required' => $this->min_percentage_shelf_life_required,
            'min_no_of_months_shelf_life_required' => $this->min_no_of_months_shelf_life_required,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_date' => $this->created_date,
            'updated_date' => $this->updated_date,
            'physical_allocation' => $this->physical_allocation,
            'removal_qty' => $this->removal_qty,
            'challan_qty' => $this->challan_qty,
            'removal_shortage_qty' => $this->removal_shortage_qty,
            'removal_excess_qty' => $this->removal_excess_qty,
            'challan_shortage_qty' => $this->challan_shortage_qty,
            'challan_excess_qty' => $this->challan_excess_qty,
            'is_first_scan' => $this->is_first_scan,
            'initial_invoice_qty' => $this->initial_invoice_qty,
            'demand_qty' => $this->demand_qty,
        ]);

        $query->andFilterWhere(['like', 'ean', $this->ean])
            ->andFilterWhere(['like', 'asin', $this->asin])
            ->andFilterWhere(['like', 'fnsku', $this->fnsku])
            ->andFilterWhere(['like', 'marketplace_title', $this->marketplace_title])
            ->andFilterWhere(['like', 'product_title', $this->product_title])
            ->andFilterWhere(['like', 'vat_cst', $this->vat_cst])
            ->andFilterWhere(['like', 'msku', $this->msku])
            ->andFilterWhere(['like', 'psku', $this->psku])
            ->andFilterWhere(['like', 'product_type', $this->product_type])
            ->andFilterWhere(['like', 'status', $this->status])
            ->andFilterWhere(['like', 'expiry_issue', $this->expiry_issue])
            ->andFilterWhere(['like', 'remarks', $this->remarks])
            ->andFilterWhere(['like', 'issues', $this->issues])
            ->andFilterWhere(['like', 'issue_type', $this->issue_type])
            ->andFilterWhere(['like', 'other_type', $this->other_type])
            ->andFilterWhere(['like', 'invoice_no', $this->invoice_no])
            ->andFilterWhere(['like', 'is_bulk_upload', $this->is_bulk_upload]);

        return $dataProvider;
    }
}
