/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


  
  NottinghamLockInfo.setLocked = function() {
       NottinghamLockInfo.locked = true;
  };
 
  NottinghamLockInfo.getLocked = function() {
     return this.locked;
  };
 
function nottinghamRemoveLockControl()
{
    nottinghamRemoveSaveControl();
    var vListButton = $('input[type=button],button');
    for (var i = 0; i < vListButton.length; i++)
    {
        vLockBtn = vListButton[i];
        if (vLockBtn.value == lang.data_entry_182 || vLockBtn.innerText.trim() == lang.data_entry_182)
        { 
            $('<i><b><br> Data hard locked</b></i>').insertBefore(vLockBtn);
              vLockBtn.remove();

        }
    }  
}

function nottinghamRemoveAddRecord()
{
    var vListButton = $('button');
    
    for (var i = 0; i < vListButton.length; i++)
    {
        vAddBtn = vListButton[i];
      
        if (vAddBtn.innerText.trim() == lang.data_entry_46 || vAddBtn.innerText.trim() == 'Add new record' ||
           vAddBtn.innerText.trim() == 'Add new record for the arm selected above' || vAddBtn.innerText.trim() == (lang.data_entry_46 + ' '+ lang.data_entry_442)
           || vAddBtn.innerText.trim() == lang.data_entry_443 || vAddBtn.innerText.trim() == (lang.data_entry_443 + ' '+ lang.data_entry_442))
        { 
              vAddBtn.remove();
        }
        
    }  
}

function nottinghamAddLockBanner()
{  
    var vHeader = document.getElementById("subheaderDiv2");
    var vLockBanner = document.getElementById("nctu_locking_banner");
    if(vLockBanner == null)
    {
       
        vLockBanner = document.createElement( 'div' );
        vLockBanner.id ="nctu_locking_banner"
        vLockBanner.style = 'color:blue;';
        vLockBanner.innerHTML = NottinghamLockInfo.getInfoText();
        vHeader.appendChild( vLockBanner );
    }
    
  
}

function nottinghamRemoveSaveControl(locktext = '<b>Data hard locked</b>')
{
    NottinghamLockInfo.setLocked();
    var vListButton = $('button'); 
    var bAddSaveText = true;
    
    for (var i = 0; i < vListButton.length; i++)
    {
        vSaveBtn = vListButton[i]; 
        if (vSaveBtn.innerText.trim() == lang.data_entry_288)
        { 
            bAddSaveText = nottinghamRemoveButton(vSaveBtn, bAddSaveText, locktext);
 
        }
        else if (vSaveBtn.innerText.trim() == lang.data_entry_409)
        { 
            bAddSaveText = nottinghamRemoveButton(vSaveBtn, bAddSaveText, locktext);
        }
        else if (vSaveBtn.innerText.trim()== lang.data_entry_275)
        { 
            bAddSaveText = nottinghamRemoveButton(vSaveBtn, bAddSaveText, locktext);  
        }
         else if (vSaveBtn.innerText.trim()== lang.data_entry_276)
        { 
            bAddSaveText = nottinghamRemoveButton(vSaveBtn, bAddSaveText, locktext);  
        }
        else if (vSaveBtn.innerText.trim() == lang.data_entry_210)
        { 
            bAddSaveText = nottinghamRemoveButton(vSaveBtn, bAddSaveText, locktext); 
        }
        else if (vSaveBtn.innerText.trim() == lang.data_entry_210)
        { 
            bAddSaveText = nottinghamRemoveButton(vSaveBtn, bAddSaveText, locktext); 
        }
        else if (vSaveBtn.innerText.trim() == lang.data_entry_212)
        { 
            bAddSaveText = nottinghamRemoveButton(vSaveBtn, bAddSaveText, locktext); 
        }
        else if (vSaveBtn.innerText.trim() == lang.data_entry_215)
        { 
            bAddSaveText = nottinghamRemoveButton(vSaveBtn, bAddSaveText, locktext); 
        }
        else if (vSaveBtn.innerText.trim() == lang.data_entry_410)
        { 
            bAddSaveText = nottinghamRemoveButton(vSaveBtn, bAddSaveText, locktext); 
        }
        else if (vSaveBtn.innerText.trim() == lang.data_entry_292)
        { 
            bAddSaveText = nottinghamRemoveButton(vSaveBtn, bAddSaveText, locktext);
        }
        else if(vSaveBtn.innerText.trim() == lang.data_entry_289)
        {
            bAddSaveText = nottinghamRemoveButton(vSaveBtn, bAddSaveText, locktext);
        }
        else if (vSaveBtn.innerText.trim() == lang.data_entry_234)
        { 
            bAddSaveText = nottinghamRemoveButton(vSaveBtn, bAddSaveText, locktext);
               
        } 
        else if (vSaveBtn.name == "nctu-alert-button")
        { 
           nottinghamRemoveButton(vSaveBtn, false, locktext);      
        }
        
        else if (vSaveBtn.innerText.trim() == lang.emlang_minimization_rando)
        { 
            nottinghamRemoveButton(vSaveBtn, false, locktext);    
        }
       
        else if (vSaveBtn.innerText.trim() == "Generate Letter")
        { 
            nottinghamRemoveButton(vSaveBtn, false, locktext); 
        }
        else if(vSaveBtn.getAttribute('title') == lang.data_entry_287)
        {
            bAddSaveText = nottinghamRemoveButton(vSaveBtn, bAddSaveText, locktext);
        } 
    }
    
   
    var vListLinks = $('.deletedoc-lnk');  
    for (var i = 0; i < vListLinks.length; i++)
    {
        vLink = vListLinks[i];
        if (vLink.innerText.trim() == lang.form_renderer_23 || vLink.innerText.trim() == lang.form_renderer_24 || vLink.innerText.trim() == lang.form_renderer_31 || vLink.innerText.trim() == lang.form_renderer_43 || vLink.innerText.trim() == lang.data_entry_459)
        { 
            nottinghamRemoveButton(vLink, false, locktext); 
 
        }
    }
    
    var vListLinks = $('.fileuploadlink'); 
    for (var i = 0; i < vListLinks.length; i++)
    {
        vLink = vListLinks[i];
        if (vLink.innerText.trim() == lang.form_renderer_23 || vLink.innerText.trim() == lang.form_renderer_24 || vLink.innerText.trim() == lang.form_renderer_31 || vLink.innerText.trim() == lang.form_renderer_43 || vLink.innerText.trim() == lang.data_entry_459)
        { 
            nottinghamRemoveButton(vLink, false, locktext); 
 
        }
    }
    
   var vLetterGenerator = $('#gen_letter'); 
   vLetterGenerator.remove();   
}

function nottinghamRemoveButton(vSaveBtn, bAddSaveText, locktext)
{
    if(bAddSaveText)
    {
        bAddSaveText = false;
        $('<i><br> '+locktext+'</i>').insertBefore(vSaveBtn);
    }
    vSaveBtn.remove();
    return bAddSaveText
}


$(document).ready(function(){
   
   // remove randomise button if it exists
   if(NottinghamLockInfo.getLocked() == true)
   {
        var vButton = $('#redcapRandomizeBtn');
        if(vButton != null)
        {
            vButton.remove(); 
            
        }
      
        nottinghamRemoveSaveControl();
    }
    nottinghamRemoveAddRecord();
    
   nottinghamAddLockBanner();
    
    
 });
 



