<?php
namespace Vanderbilt\FileDownloadTrackingExternalModule;

use Exception;
use REDCap;
use ExternalModules\AbstractExternalModule;
use ExternalModules\ExternalModules;

class FileDownloadTrackingExternalModule extends AbstractExternalModule
{
    public function __construct()
    {
        parent::__construct();
    }

    function hook_every_page_top(){
        $download_field = $this->getProjectSetting('download-field');
        $tracking_field = $this->getProjectSetting('tracking-field');
        $url = $this->getUrl('trackField.php');
        echo "<script>
                $(document).ready(function() {
                    var download_field = " . json_encode($download_field) . ";
                    var tracking_field = " . json_encode($tracking_field) . ";
                    var url = " . json_encode($url) . ";
                    
                    for (var i = 0; i < download_field.length; i++) {
                       $('[sq_id=\"'+download_field[i]+'\"] .rc_attach').append('<input type=\'hidden\' id=\"'+download_field[i]+'_tracking\" parent_id=\"'+tracking_field[i]+'\" tracking=\'0\'>');
                       
                       $('[sq_id=\"'+download_field[i]+'\"] .rc_attach').click(function() {
                             $(this).find('input').prop('tracking','1');
                             console.log($(this).find('input').attr('parent_id'));
                             var name = $(this).find('input').attr('parent_id');
                             document.forms['form'][name].value='1';
                       });
                    }
                    
                });
                </script>";
    }

    function redcap_module_configuration_settings($project_id, $projectSettings){
        foreach ($projectSettings[0]['sub_settings'] as $index => $subsetting){
            if($subsetting['key'] == 'tracking-field'){
                $sql = "SELECT field_name,element_label
					FROM redcap_metadata
					WHERE project_id = ?
					AND element_type = ?
					ORDER BY field_order";
                $result = $this->query($sql, [$project_id ,"yesno"]);
                while ($row = $result->fetch_assoc()) {
                    $row['element_label'] = strip_tags(nl2br($row['element_label']));
                    if (strlen($row['element_label']) > 30) {
                        $row['element_label'] = substr($row['element_label'], 0, 20) . "... " . substr($row['element_label'], -8);
                    }
                    $choices[] = ['value' => $row['field_name'], 'name' => $row['field_name'] . " - " . htmlspecialchars($row['element_label'])];
                }
                $projectSettings[0]['sub_settings'][$index]['choices'] = $choices;
            }
        }
        return $projectSettings;
    }
}

?>