<?php
$connect = mysqli_connect("localhost", "root", "", "recursive");
$query = "
 SELECT * FROM cuidades
";
$result = mysqli_query($connect, $query);
$output = array();
while($row = mysqli_fetch_array($result)) 
{  
    $sub_data["id"] = $row[0];
    $sub_data["name"] = $row[1];
    $sub_data["text"] = $row[1];
    $sub_data["parent_id"] = $row[2];
	$sub_data["a_attr"] = $row[3];
    $data[] = $sub_data;
}  
$itemsByReference = array();

// Build array of item references:
foreach($data as $key => &$item) {
    $itemsByReference[$item["id"]] = &$item;
    // Children array:
    $itemsByReference[$item["id"]]["children"] = array();
    // Empty data class (so that json_encode adds "data: {}" ) 
    //$itemsByReference[$item["id"]]["data"] = new StdClass();
    $itemsByReference[$item['id']]['a_attr'] = new StdClass();
 }
  
 // Set items as children of the relevant parent item.
 foreach($data as $key => &$item)
    if($item['parent_id'] && isset($itemsByReference[$item['parent_id']])){
       $itemsByReference [$item['parent_id']]['children'][] = &$item;
	}
  
 // Remove items that were added to parents elsewhere:
 foreach($data as $key => &$item) {
    if(empty($item['children'])) {
			
        $item['a_attr']->href = 'https://wikipedia.org/wiki/'.$item["name"];
    }
    if($item['parent_id'] && isset($itemsByReference[$item['parent_id']]))
       unset($data[$key]);
 }
 // Encode:
 echo json_encode($data);
?>