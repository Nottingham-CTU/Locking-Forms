/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var lockinfo;

function removeLockControl()
{
    removeSaveControl();
    var vListButton = $('input[type=button]');
    for (var i = 0; i < vListButton.length; i++)
    {
        vLockBtn = vListButton[i];
        if (vLockBtn.value == lang.data_entry_182)
        { 
            $('<i><b><br> Data hard locked</b></i>').insertBefore(vLockBtn);
              vLockBtn.remove();

        }
    }  
}

function removeAddRecord(txt)
{
    addLockBanner(txt);
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

function addLockBanner(txt)
{
    lockinfo = txt;
    var vHeader = document.getElementById("subheaderDiv2");
    var vLockBanner = document.createElement( 'div' );
    vLockBanner.style = 'color:blue;';
    vLockBanner.innerHTML = txt;
    vHeader.appendChild( vLockBanner );
  
}

function removeSaveControl(locktext = '<b>Data hard locked</b>')
{
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

// remove ranodise button if it exists
$(document).ready(function(){
   
    var vButton = $('#redcapRandomizeBtn'); 
    if(vButton != null)
    {
        vButton.remove();
    }
    removeAddRecord(lockinfo);
    
    
 });
 



