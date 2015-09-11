<?php

/**
 * This is the model class for table "breed_info".
 *
 * The followings are the available columns in table 'breed_info':
 * @property integer $breedId
 * @property string $DA
 * @property string $BZ
 * @property string $DA_link
 * @property string $BZ_link
 */
class BreedInfo extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return BreedInfo the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'breed_info';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('breedId, DA, BZ', 'required'),
			array('breedId', 'numerical', 'integerOnly'=>true),
			array('DA_link, BZ_link', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('breedId, DA, BZ, DA_link, BZ_link', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'breedId' => 'Breed',
			'DA' => 'Da',
			'BZ' => 'Bz',
			'DA_link' => 'Da Link',
			'BZ_link' => 'Bz Link',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('breedId',$this->breedId);
		$criteria->compare('DA',$this->DA,true);
		$criteria->compare('BZ',$this->BZ,true);
		$criteria->compare('DA_link',$this->DA_link,true);
		$criteria->compare('BZ_link',$this->BZ_link,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}

	public function parse($row)
	{	
		$data = array();
		foreach ($row as $key => $value) 
		{
			$newRow['docs'] = $value->DA;
			$newRow['more'] = $value->BZ;
			$data[] = $newRow; 		
		}var_dump($data);die();
		$newRow = array();
		$newRow['name'] = $row['name'];
		return $newRow;
	}
}