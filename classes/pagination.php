<?php
if ( ! defined( 'ABSPATH' ) )
	 exit;

class XYZ_WP_Pagination
{

	var $page_number=1;
	var $sql;
	var $sql_params;
	var $limit;
	var $data_start;
	var $is_valid_page=true;
	var $result="";
	var $links="";
	var $style_class="xyz_wp_pagination";
	var $has_next=0;

	function __construct($sql,$sql_params=array(),$limit=0)
	{
		if($limit == 0){
			$limit = 3;
		}
		//echo $sql;
		$this->page_number = isset($_GET['page_number']) ? absint($_GET['page_number']) : 1;

		$this->sql=$sql;
		$this->sql_params=$sql_params;
        
		$this->limit=$limit;
		$this->data_start=intval((($this->limit*$this->page_number)-$this->limit));

		global $wpdb;
		$this->result=$wpdb->get_results($wpdb->prepare($sql." LIMIT $this->data_start,$this->limit",$sql_params));
	   
		if(count($this->result)==0)
		{
			
			$this->is_valid_page=false;
		}
		else
		{
			
			$end_limit=1;
			switch($this->page_number)
			{
				case 1:
						$end_limit=5*$this->limit;
						break;
						
				case 2:
						$end_limit=4*$this->limit;
						break;
				default:
						$end_limit=3*$this->limit;
			}
			
			$res_next=$wpdb->get_results($wpdb->prepare($sql." LIMIT ".($this->data_start+$this->limit).",".$end_limit,$sql_params));
			$this->has_next=ceil(count($res_next)/$this->limit);
		}
	}

	function xyz_cls_get_result()
	{
		return $this->result;
	}

	function xyz_cls_get_page_number()
	{
		return $this->page_number;
	}

	function links($include_post=true)
	{
		if($this->is_valid_page)
		{
			static $counter=0;
			$counter++;
			if($_POST && $include_post)
			{
				$this->links.="<form name=\"xyz_wp_pagination".$counter."\"  method=post>";
				foreach ($_POST as $k=>$v)
				{
					$this->links.="<input type=hidden name=\"".$k."\" value=\"".$v."\">";
				}
				$this->links.="</form>".
						"<script type='text/javascript'>".
						"function xyz_wp_pagination".$counter."_submit(act)".
						"{document.forms['xyz_wp_pagination".$counter."'].action=''+act+'';document.forms['xyz_wp_pagination".$counter."'].submit()}".
						"</script>";
			}


			if($this->style_class!="")
				$styleclass="class=\"".$this->style_class."\"";

			$this->links.="<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" $styleclass>
			<tr >
			";

	


			
			if($this->page_number>1)
			{
				if($_POST  && $include_post)
				{
					$this->links.="<td >
					<a href=\"javascript:xyz_wp_pagination".$counter."_submit('".$this->make_url($this->page_number-1)."')\"  >&laquo;</a>
					</td>";
			
				}
				else
				{
					$this->links.="<td >
					<a href=\"".$this->make_url($this->page_number-1)."\"  >&laquo;</a>
					</td>";
				}
			}
			
			
			
			switch($this->page_number)
			{
				case 1:
				case 2:
						$k=1;
						break;
// 				case ($this->page_number+$this->has_next):
				//case ($this->page_number+$this->has_next):
// 						$k=1;
// 						break;
				
				default:

						if($this->has_next==0)
							$k=$this->page_number-4;
						elseif($this->has_next==1)
							$k=$this->page_number-3;
						else
							$k=$this->page_number-2;
			}
			if($k<=0)$k=1;
			
			for($j=0;$j<5 && $k <= ($this->page_number+$this->has_next) ;$j++)
			{

				if($k==$this->page_number)
					$this->links.="<td ><span id='select_span'>".$this->page_number."</span></td>";
				else
				{
					if($_POST && $include_post)
					{
						$this->links.="<td >
						<a href=\"javascript:xyz_wp_pagination".$counter."_submit('".$this->make_url($k)."')\"  ><span class='xyz_cls_page_span'>".$k."</span></a>
						</td>";
					
					}
					else
					{
						$this->links.="<td >
						<a href=\"".$this->make_url($k)."\"  ><span class='xyz_cls_page_span'>".$k."</span></a>
						</td>";
					}
				}
				
				$k++;
				
			}
			
			if($this->has_next>0)
			{
				if($_POST && $include_post)
				{
					$this->links.="<td >
					<a href=\"javascript:xyz_wp_pagination".$counter."_submit('".$this->make_url($this->page_number+1)."')\"  >&raquo;</a>
					</td>";
			
				}
				else
				{
					$this->links.="<td >
					<a href=\"".$this->make_url($this->page_number+1)."\"  >&raquo;</a>
					</td>";
				}
			}
			
			$this->links.="</tr>
			</table>
			";
			if($this->page_number==1 && $this->has_next==0)
				$this->links="";
		}
		return $this->links;
	}




	function make_url($page_num)
	{
		$page = isset($_GET['page']) ?absint($_GET['page']) : 1;

		$params='';
		foreach($_GET as $k=> $v)
		{
			if($k!="page_number")
				$params.=$k."=".$v.'&';
		}

//		if($params!='')
//			$params=substr($params,0,-1);

		$http="http://";
		if (stripos(get_option('siteurl'), 'https://') === 0)
			$http="https://";

		$uri_parts = explode('?', $_SERVER['REQUEST_URI']);
		$var=$uri_parts[0];
//		$var=substr($var,0,strrpos($var,"/"));
		$hostdir= $http.$_SERVER['HTTP_HOST']."".$var;
		
		return $hostdir."?".$params.'page_number='.$page_num;
	}
};
?>