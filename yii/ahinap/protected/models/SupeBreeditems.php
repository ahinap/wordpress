<?php

/**
 * This is the model class for table "supe_breeditems".
 *
 * The followings are the available columns in table 'supe_breeditems':
 * @property integer $itemid
 * @property integer $catid
 * @property integer $uid
 * @property integer $tid
 * @property string $username
 * @property string $subject
 * @property string $subjectimage
 * @property integer $rates
 * @property string $dateline
 * @property string $lastpost
 * @property integer $viewnum
 * @property integer $replynum
 * @property integer $allowreply
 * @property integer $grade
 */
class SupeBreeditems extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return SupeBreeditems the static model class
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
		return 'supe_breeditems';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('catid, uid, tid, rates, viewnum, replynum, allowreply, grade', 'numerical', 'integerOnly'=>true),
			array('username', 'length', 'max'=>15),
			array('subject, subjectimage', 'length', 'max'=>80),
			array('dateline, lastpost', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('itemid, catid, uid, tid, username, subject, subjectimage, rates, dateline, lastpost, viewnum, replynum, allowreply, grade', 'safe', 'on'=>'search'),
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
			'itemid' => 'Itemid',
			'catid' => 'Catid',
			'uid' => 'Uid',
			'tid' => 'Tid',
			'username' => 'Username',
			'subject' => 'Subject',
			'subjectimage' => 'Subjectimage',
			'rates' => 'Rates',
			'dateline' => 'Dateline',
			'lastpost' => 'Lastpost',
			'viewnum' => 'Viewnum',
			'replynum' => 'Replynum',
			'allowreply' => 'Allowreply',
			'grade' => 'Grade',
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

		$criteria->compare('itemid',$this->itemid);
		$criteria->compare('catid',$this->catid);
		$criteria->compare('uid',$this->uid);
		$criteria->compare('tid',$this->tid);
		$criteria->compare('username',$this->username,true);
		$criteria->compare('subject',$this->subject,true);
		$criteria->compare('subjectimage',$this->subjectimage,true);
		$criteria->compare('rates',$this->rates);
		$criteria->compare('dateline',$this->dateline,true);
		$criteria->compare('lastpost',$this->lastpost,true);
		$criteria->compare('viewnum',$this->viewnum);
		$criteria->compare('replynum',$this->replynum);
		$criteria->compare('allowreply',$this->allowreply);
		$criteria->compare('grade',$this->grade);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}

	public function parse($row)
	{	
		$data = array();
		// for ($i=0; $i < 148; $i++) 
		// { 
			
			$i = 0;
			foreach ($row as $key => $value) 
			{
				$i++;
				$newRow['name'] = $value->subject;
				$newRow['url'] = 'url';
				$newRow['title'] = $value->subject;
				$newRow['ename'] = 'englishname';
				$newRow['alias'] = 'alias';
				$newRow['weight'] = 'weight';
				$newRow['iq'] = 'iq';
				$newRow['length'] = 'height';
				$newRow['years'] = 'life';
				$newRow['place'] = 'origin';
				$newRow['shape'] = 'shape';
				if($value->catid==6)
				{
					$newRow['wool'] = '长毛猫';
				}
				if($value->catid==10)
				{
					$newRow['wool'] = '短毛猫';
				}
				if($value->catid==11)
				{
					$newRow['wool'] = '无毛猫';
				}
				if($value->catid==12)
				{
					$newRow['wool'] = '土猫';
				}
				$newRow['function'] = 'function';
				// $breed_id = $value->id;
				
				// $criteria = new CDbCriteria();
				// $modelInfo = BreedInfo::model()->findAll($criteria);
				
				// foreach ($modelInfo as $key => $value) 
				// {
				// 	if ($breed_id==$value->breedId) 
				// 	{
				// 		$newRow['资料'] = '';/*substr(utf8_encode($value->DA),0,500);*/
				// 		$newRow['more'] = '';/*substr($value->BZ,0,500);*/
				// 		$newRow['morelink'] = $value->DA_link;
				// 	}
				// }
				$data['item'.$i] = $newRow; 	
			}
		// }
		// var_dump($data);die();
		return $data;
	}

	public function parseCutie($row)
	{	
		$data = array();
		// for ($i=0; $i < 148; $i++) 
		// { 
			
			$i = 0;
			foreach ($row as $key => $value) 
			{
				$i++;
				$newRow['name'] = $value->subject;
				$newRow['url'] = 'url';
				$newRow['title'] = $value->subject;
				$newRow['ename'] = 'englishname';
				$newRow['alias'] = 'alias';
				$newRow['weight'] = 'weight';
				$newRow['iq'] = 'iq';
				$newRow['length'] = 'height';
				$newRow['years'] = 'life';
				$newRow['place'] = 'origin';
				$newRow['shape'] = 'shape';
				$newRow['wool'] = '';
				if ($value->catid==13) {
					$newRow['function'] = '兔兔';
				}
				if ($value->catid==14) {
					$newRow['function'] = '鼠鼠';
				}
				if ($value->catid==15) {
					$newRow['function'] = '爬行';
				}
				if ($value->catid==16) {
					$newRow['function'] = '昆虫';
				}
				if ($value->catid==17) {
					$newRow['function'] = '其他';
				}
				// $newRow['function'] = 'function';
				// $breed_id = $value->id;
				
				// $criteria = new CDbCriteria();
				// $modelInfo = BreedInfo::model()->findAll($criteria);
				
				// foreach ($modelInfo as $key => $value) 
				// {
				// 	if ($breed_id==$value->breedId) 
				// 	{
				// 		$newRow['资料'] = '';/*substr(utf8_encode($value->DA),0,500);*/
				// 		$newRow['more'] = '';/*substr($value->BZ,0,500);*/
				// 		$newRow['morelink'] = $value->DA_link;
				// 	}
				// }
				$data['item'.$i] = $newRow; 	
			}
		// }
		// var_dump($data);die();
		return $data;
	}

	public function parseAqanimal($row)
	{	
		$data = array();
		// for ($i=0; $i < 148; $i++) 
		// { 
			
			$i = 0;
			foreach ($row as $key => $value) 
			{
				$i++;
				$newRow['name'] = $value->subject;
				$newRow['url'] = 'url';
				$newRow['title'] = $value->subject;
				$newRow['ename'] = 'englishname';
				$newRow['alias'] = 'alias';
				$newRow['weight'] = 'weight';
				$newRow['iq'] = 'iq';
				$newRow['length'] = 'height';
				$newRow['years'] = 'life';
				$newRow['place'] = 'origin';
				$newRow['shape'] = 'shape';
				$newRow['wool'] = '';
				if ($value->catid==26) {
					$newRow['function'] = '海水鱼';
				}
				if ($value->catid==27) {
					$newRow['function'] = '淡水鱼';
				}
				if ($value->catid==28) {
					$newRow['function'] = '金鱼专区';
				}
				if ($value->catid==29) {
					$newRow['function'] = '锦鲤专区';
				}
				// $newRow['function'] = 'function';
				// $breed_id = $value->id;
				
				// $criteria = new CDbCriteria();
				// $modelInfo = BreedInfo::model()->findAll($criteria);
				
				// foreach ($modelInfo as $key => $value) 
				// {
				// 	if ($breed_id==$value->breedId) 
				// 	{
				// 		$newRow['资料'] = '';/*substr(utf8_encode($value->DA),0,500);*/
				// 		$newRow['more'] = '';/*substr($value->BZ,0,500);*/
				// 		$newRow['morelink'] = $value->DA_link;
				// 	}
				// }
				$data['item'.$i] = $newRow; 	
			}
		// }
		// var_dump($data);die();
		return $data;
	}
}