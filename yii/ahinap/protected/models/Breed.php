<?php

/**
 * This is the model class for table "breed".
 *
 * The followings are the available columns in table 'breed':
 * @property integer $id
 * @property integer $cateId
 * @property integer $gid
 * @property integer $fid
 * @property string $arg
 * @property string $name
 * @property string $englishname
 * @property string $alias
 * @property string $thumb
 * @property string $pinyin
 * @property string $bodysize
 * @property string $function
 * @property integer $hairlength
 * @property integer $hairlost
 * @property integer $friendly
 * @property string $height
 * @property string $weight
 * @property string $price
 * @property integer $care
 * @property string $origin
 * @property string $habits
 * @property string $reproduction
 * @property string $xixing
 * @property string $foodtype
 * @property string $life
 * @property integer $type
 * @property integer $hot
 * @property string $dateline
 * @property string $GUID
 * @property integer $closed
 */
class Breed extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Breed the static model class
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
		return 'breed';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('gid, GUID', 'required'),
			array('cateId, gid, fid, hairlength, hairlost, friendly, care, type, hot, closed', 'numerical', 'integerOnly'=>true),
			array('arg, name, height, weight, price, origin, reproduction, xixing, foodtype, life', 'length', 'max'=>20),
			array('englishname, alias, function, habits', 'length', 'max'=>50),
			array('thumb', 'length', 'max'=>60),
			array('pinyin', 'length', 'max'=>1),
			array('bodysize, dateline', 'length', 'max'=>10),
			array('GUID', 'length', 'max'=>32),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, cateId, gid, fid, arg, name, englishname, alias, thumb, pinyin, bodysize, function, hairlength, hairlost, friendly, height, weight, price, care, origin, habits, reproduction, xixing, foodtype, life, type, hot, dateline, GUID, closed', 'safe', 'on'=>'search'),
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
			'id' => 'ID',
			'cateId' => 'Cate',
			'gid' => 'Gid',
			'fid' => 'Fid',
			'arg' => 'Arg',
			'name' => 'Name',
			'englishname' => 'Englishname',
			'alias' => 'Alias',
			'thumb' => 'Thumb',
			'pinyin' => 'Pinyin',
			'bodysize' => 'Bodysize',
			'function' => 'Function',
			'hairlength' => 'Hairlength',
			'hairlost' => 'Hairlost',
			'friendly' => 'Friendly',
			'height' => 'Height',
			'weight' => 'Weight',
			'price' => 'Price',
			'care' => 'Care',
			'origin' => 'Origin',
			'habits' => 'Habits',
			'reproduction' => 'Reproduction',
			'xixing' => 'Xixing',
			'foodtype' => 'Foodtype',
			'life' => 'Life',
			'type' => 'Type',
			'hot' => 'Hot',
			'dateline' => 'Dateline',
			'GUID' => 'Guid',
			'closed' => 'Closed',
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

		$criteria->compare('id',$this->id);
		$criteria->compare('cateId',$this->cateId);
		$criteria->compare('gid',$this->gid);
		$criteria->compare('fid',$this->fid);
		$criteria->compare('arg',$this->arg,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('englishname',$this->englishname,true);
		$criteria->compare('alias',$this->alias,true);
		$criteria->compare('thumb',$this->thumb,true);
		$criteria->compare('pinyin',$this->pinyin,true);
		$criteria->compare('bodysize',$this->bodysize,true);
		$criteria->compare('function',$this->function,true);
		$criteria->compare('hairlength',$this->hairlength);
		$criteria->compare('hairlost',$this->hairlost);
		$criteria->compare('friendly',$this->friendly);
		$criteria->compare('height',$this->height,true);
		$criteria->compare('weight',$this->weight,true);
		$criteria->compare('price',$this->price,true);
		$criteria->compare('care',$this->care);
		$criteria->compare('origin',$this->origin,true);
		$criteria->compare('habits',$this->habits,true);
		$criteria->compare('reproduction',$this->reproduction,true);
		$criteria->compare('xixing',$this->xixing,true);
		$criteria->compare('foodtype',$this->foodtype,true);
		$criteria->compare('life',$this->life,true);
		$criteria->compare('type',$this->type);
		$criteria->compare('hot',$this->hot);
		$criteria->compare('dateline',$this->dateline,true);
		$criteria->compare('GUID',$this->GUID,true);
		$criteria->compare('closed',$this->closed);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}

	public function parse($row)
	{	
		$data = array();
			$function = array(
				1 => '伴侣犬',
				2 => '绒毛犬',
				3 => '牧羊犬',
				4 => '比赛犬',
				5 => '梗类犬',
				6 => '畜牧与守卫犬',
				7 => '嗅觉猎犬',
				8 => '枪猎犬',
				9 => '狩望猎犬',
				10 => '古老犬',
				11 => '警犬',
				12 => '工作犬',
				13 => '看家犬',
				14 => '护卫犬',
				15 => '猎狐犬',
				16 => '捕鼠犬',
				17 => '牧牛犬',
				18 => '斗牛犬',
				19 => '拉雪橇犬',
				20 => '救助犬',
				21 => '玩赏犬',
				22 => '缉毒犬',
				23 => '搜查犬',
				24 => '导盲犬',
				25 => '爆破犬',
				26 => '玩具犬'
			);
			$i = 0;
			foreach ($row as $key => $value) 
			{
				$i++;
				$newRow['key'] = $value->name;
				$newRow['name'] = $value->name;
				

				$breed_id = $value->id;
				
				$criteria = new CDbCriteria();
				$modelInfo = BreedInfo::model()->findAll($criteria);
				
				foreach ($modelInfo as $key => $values) 
				{
					if ($breed_id==$values->breedId) 
					{				
					$newRow['url'] = substr($values->DA_link, 0,4)=='http'?$values->DA_link:'http://www.ttpet.com';
						$newRow['title'] = $value->name;
						$newRow['ename'] = strlen($value->englishname)==0?"无":$value->englishname;
						$newRow['alias'] = $value->alias;
						$newRow['weight'] = $value->weight;
						$newRow['Mweight'] = $value->weight;
						$newRow['iq'] = '-';
						$newRow['Miq'] = '-';
						$newRow['length'] = $value->height;
						$newRow['Mlength'] = $value->height;
						$newRow['years'] = $value->life;
						$newRow['Myears'] = $value->life;
						$newRow['place'] = $value->origin;
						$newRow['Mplace'] = $value->origin;		
						if($value->bodysize==3)
						{
							$newRow['shape'] = '大型犬';
							$newRow['Mshape'] = '大型犬';
						}
						if($value->bodysize==2)
						{
							$newRow['shape'] = '中型犬';
							$newRow['Mshape'] = '中型犬';
						}
						if($value->bodysize==1)
						{
							$newRow['shape'] = '小型犬';
							$newRow['Mshape'] = '小型犬';
						}

						if($value->hairlength==1)
						{
							$newRow['wool'] = '长毛';
							$newRow['Mwool'] = '长毛';
						}
						if($value->hairlength==2)
						{
							$newRow['wool'] = '短毛';
							$newRow['Mwool'] = '短毛';
						}
						if($value->hairlength==3)
						{
							$newRow['wool'] = '无毛';
							$newRow['Mwool'] = '无毛';
						}
						$newRow['function'] = '';
						$dog_func = explode(',', $value->function);
						foreach ($dog_func as $key => $value_num) {
							foreach ($function as $key_num => $val) {
								if ($value_num == $key_num) 
								{
									 $newRow['function'].= $val.'|'; 
								}
							}
						}
						$newRow['information'] = mb_substr($values->DA, 0, 150, 'utf-8');/*substr(utf8_encode($value->DA),0,500);*/
						$newRow['more'] = '犬种标准';/*;*/
						$newRow['morelink'] = $values->BZ_link;
						$newRow['more'] = '狗狗详情/狗狗档案';/*;*/
						$newRow['morelink'] = $values->DA_link;
					}
				}
				$data['item'] = $newRow; 
				$data_res[]= $data;
			}
		return $data_res;
	}

	public function parseCattie($row)
	{	
		$data = array();
			$i = 0;
			foreach ($row as $key => $value) 
			{
				$i++;
				$newRow['key'] = $value->name;
				$newRow['name'] = $value->name;
				
				
				
				
				$breed_id = $value->id;
				
				$criteria = new CDbCriteria();
				$modelInfo = BreedInfo::model()->findAll($criteria);
				
				foreach ($modelInfo as $key => $values) 
				{
					if ($breed_id==$values->breedId) 
					{						
						$newRow['url'] = substr($values->DA_link, 0,4)=='http'?$values->DA_link:'http://www.ttpet.com';
						$newRow['title'] = $value->name;
						$newRow['ename'] = $value->englishname;
						$newRow['alias'] = $value->alias;
						$newRow['weight'] = $value->weight;
						$newRow['Mweight'] = $value->weight;
						$newRow['iq'] = '-';
						$newRow['Miq'] = '-';
						$newRow['length'] = $value->height;
						$newRow['Mlength'] = $value->height;
						$newRow['years'] = $value->life;
						$newRow['Myears'] = $value->life;
						$newRow['place'] = $value->origin;
						$newRow['Mplace'] = $value->origin;
						if($value->bodysize==3)
						{
							$newRow['shape'] = '小型猫';
							$newRow['Mshape'] = '小型猫';
						}
						if($value->bodysize==2)
						{
							$newRow['shape'] = '中型猫';
							$newRow['Mshape'] = '中型猫';
						}
						if($value->bodysize==1)
						{
							$newRow['shape'] = '大型猫';
							$newRow['Mshape'] = '大型猫';
						}
						

						if($value->hairlength==1)
						{
							$newRow['wool'] = '长毛';
							$newRow['Mwool'] = '长毛';
						}
						if($value->hairlength==2)
						{
							$newRow['wool'] = '短毛';
							$newRow['Mwool'] = '短毛';
						}
						if($value->hairlength==3)
						{
							$newRow['wool'] = '无毛';
							$newRow['Mwool'] = '无毛';
						}
						$newRow['function'] = '-';
						$newRow['information'] = mb_substr($values->DA, 0, 150, 'utf-8');/*substr(utf8_encode($value->DA),0,500);*/
						$newRow['more'] = '猫猫标准';/*;*/
						$newRow['morelink'] = $values->BZ_link;
						$newRow['more'] = '猫猫详情/猫咪档案';/*;*/
						$newRow['morelink'] = substr($values->DA_link, 0,4)=='http'?$values->DA_link:'http://www.ttpet.com';
					}
				}

				
				$data['item'] = $newRow; 	
				$data_res[]= $data;
			}
		return $data_res;
	}
}