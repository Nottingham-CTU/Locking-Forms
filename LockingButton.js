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
 
function removeLockControl()
{
    removeSaveControl();
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

function removeAddRecord()
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

function addLockBanner()
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

function removeSaveControl(locktext = '<b>Data hard locked</b>')
{
    NottinghamLockInfo.setLocked();
    var vListButton = $('button'); 
    var bAddSaveText = true;
    
    for (var i = 0; i < vListButton.length; i++)
    {
        vSaveBtn = vListButton[i]; 
        if (vSaveBtn.innerText.trim() == lang.data_entry_288)
        { 
            bAddSaveText = removeButton(vSaveBtn, bAddSaveText, locktext);
 
        }
        else if (vSaveBtn.innerText.trim() == lang.data_entry_409)
        { 
            bAddSaveText = removeButton(vSaveBtn, bAddSaveText, locktext);
        }
        else if (vSaveBtn.innerText.trim()== lang.data_entry_275)
        { 
            bAddSaveText = removeButton(vSaveBtn, bAddSaveText, locktext);  
        }
         else if (vSaveBtn.innerText.trim()== lang.data_entry_276)
        { 
            bAddSaveText = removeButton(vSaveBtn, bAddSaveText, locktext);  
        }
        else if (vSaveBtn.innerText.trim() == lang.data_entry_210)
        { 
            bAddSaveText = removeButton(vSaveBtn, bAddSaveText, locktext); 
        }
        else if (vSaveBtn.innerText.trim() == lang.data_entry_210)
        { 
            bAddSaveText = removeButton(vSaveBtn, bAddSaveText, locktext); 
        }
        else if (vSaveBtn.innerText.trim() == lang.data_entry_212)
        { 
            bAddSaveText = removeButton(vSaveBtn, bAddSaveText, locktext); 
        }
        else if (vSaveBtn.innerText.trim() == lang.data_entry_215)
        { 
            bAddSaveText = removeButton(vSaveBtn, bAddSaveText, locktext); 
        }
        else if (vSaveBtn.innerText.trim() == lang.data_entry_410)
        { 
            bAddSaveText = removeButton(vSaveBtn, bAddSaveText, locktext); 
        }
        else if (vSaveBtn.innerText.trim() == lang.data_entry_292)
        { 
            bAddSaveText = removeButton(vSaveBtn, bAddSaveText, locktext);
        }
        else if(vSaveBtn.innerText.trim() == lang.data_entry_289)
        {
            bAddSaveText = removeButton(vSaveBtn, bAddSaveText, locktext);
        }
        else if (vSaveBtn.innerText.trim() == lang.data_entry_234)
        { 
            bAddSaveText = removeButton(vSaveBtn, bAddSaveText, locktext);
               
        } 
        else if (vSaveBtn.innerText.trim() == "Send SMS")
        { 
           removeButton(vSaveBtn, false, locktext);      
        }
        else if (vSaveBtn.innerText.trim() == "Send Email")
        { 
            removeButton(vSaveBtn, false, locktext);  
        }
        else if (vSaveBtn.innerText.trim() == lang.emlang_minimization_rando)
        { 
            removeButton(vSaveBtn, false, locktext);    
        }
       
        else if (vSaveBtn.innerText.trim() == "Generate Letter")
        { 
            removeButton(vSaveBtn, false, locktext); 
        }
        else if(vSaveBtn.getAttribute('title') == lang.data_entry_287)
        {
            bAddSaveText = removeButton(vSaveBtn, bAddSaveText, locktext);
        } 
    }
    
   
    var vListLinks = $('.deletedoc-lnk'); 
    for (var i = 0; i < vListLinks.length; i++)
    {
        vLink = vListLinks[i];
        if (vLink.innerText.trim() == lang.form_renderer_43)
        { 
            removeButton(vLink, false, locktext); 
 
        }
    }
    
   var vLetterGenerator = $('#gen_letter'); 
   vLetterGenerator.remove();   
}

function removeButton(vSaveBtn, bAddSaveText, locktext)
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
      
        removeSaveControl();
    }
    removeAddRecord();
    
   addLockBanner();
    
    
 });
 



