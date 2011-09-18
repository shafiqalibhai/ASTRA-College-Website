
function highlightPick() {
	for(i=0;i<={PICK_TYPE_COUNT};i++) {
		if (thingy=document.getElementById('PICK_TYPE_'+i)) {
			thingycol=(thingy.firstChild.firstChild.checked? '#BBB':'#DDD');
			thingy.style.backgroundColor=thingycol;
			if (i && thingy.nextSibling) {
				thingy.nextSibling.style.backgroundColor=thingycol;
			}
			
			
			subMenu=document.getElementById("PICK_TYPE_SUB_"+i)
			if (thingy.firstChild.firstChild.checked) {
				if (i=={PICK_TYPE_COUNT}) {
					if(subMenu) {subMenu.style.visibility="visible";}
					showColourPick();
				} else {
					hideColourPick();
				}
			} else {
				if(subMenu) {subMenu.style.visibility="hidden";}
			}
			
			thingyEdit=document.getElementById('PICK_TYPE_EDIT_'+i);
			if (thingyEdit) {thingyEdit.style.display=(thingy.firstChild.firstChild.checked?'':'none')};
		}
	}
}

function showColourPick() {
	pickacol=document.getElementById('pick_a_colour');
	pickacol.style.display='';
	pickacol.parentNode.style.backgroundColor='#BBB';
	
}
function hideColourPick() {
	pickacol=document.getElementById('pick_a_colour');
	pickacol.style.display='none';
	pickacol.parentNode.style.backgroundColor='#DDD';
	
}

function cView(td_item,outflag) {
	if (td_item.id!=('cp'+document.getElementById('newnameC').value)) {
		td_item.style.borderColor=(outflag? "#BBB":"#FFF");
	}
}

function cSet(ccolour) {
	var prevC=document.getElementById('cp'+document.getElementById('newnameC').value);

	if (prevC) {prevC.style.borderColor="#BBB";}
	document.getElementById('newnameC').value=ccolour;
	document.getElementById('cp'+ccolour).style.borderColor="#000";
	if (document.getElementById('PICK_TYPE_'+{PICK_TYPE_COUNT}).firstChild.firstChild.checked) {
		document.getElementById('newname').style.color='#'+ccolour;
	} else {
		document.getElementById('type_command').value="recolour";
		document.getElementById('event_input_form').submit();
	}
}

function setSelVal(selobj, val) {
	var index;
	for(index = 0; index < selobj.length; index++) {
		if(selobj[index].value == val) selobj.selectedIndex = index;
	}
}

function rollTime() {
	var nowTime=getEDate('event_date');
	setEDate('event_date',nowTime);  // corrects date if out of month range.
setEDate('event_date_finish',getEDate('event_date_finish')+(nowTime-initialTime));
	initialTime=nowTime;
}

function checkFinish() {
setEDate("event_date_finish",Math.max(getEDate('event_date'),getEDate('event_date_finish')));
}


function getEDate(eid) {
	var dayel=document.getElementById(eid+'_day');
	var day=dayel[dayel.selectedIndex].value;
	
	var monel=document.getElementById(eid+'_month');
	var mon=monel.selectedIndex-1;
	
	var yearel=document.getElementById(eid+'_year');
	var year=yearel[yearel.selectedIndex].value;
	
	var hourel=document.getElementById(eid+'_hour');
	var hour=hourel[hourel.selectedIndex].value;
	
	var minel=document.getElementById(eid+'_minute');
	var min=minel[minel.selectedIndex].value;
	
	var newDate=new Date(year, mon, day, hour, min, 0);
	return newDate.getTime();
}

function setEDate(eid,t) {
	newDate = new Date();
	newDate.setTime(t);

	setSelVal(document.getElementById(eid+'_day'),newDate.getDate());
	document.getElementById(eid+'_month').selectedIndex=newDate.getMonth()+1;
	setSelVal(document.getElementById(eid+'_year'),newDate.getFullYear());
	setSelVal(document.getElementById(eid+'_hour'),newDate.getHours());
	setSelVal(document.getElementById(eid+'_minute'),newDate.getMinutes());
}

initialTime=0;		
function cinit() {
	document.getElementById('PICK_TYPE_'+{PICK_TYPE_COUNT}).style.display='';
	highlightPick();
	
	initialTime=getEDate('event_date');
	document.getElementById('event_date_day').onchange=new Function('rollTime();');
	document.getElementById('event_date_month').onchange=new Function('rollTime();');
	document.getElementById('event_date_year').onchange=new Function('rollTime();');
	document.getElementById('event_date_minute').onchange=new Function('rollTime();');
	document.getElementById('event_date_hour').onchange=new Function('rollTime();');

	document.getElementById('event_date_finish_day').onchange=new Function('checkFinish();');
	document.getElementById('event_date_finish_month').onchange=new Function('checkFinish();');
	document.getElementById('event_date_finish_year').onchange=new Function('checkFinish();');
	document.getElementById('event_date_finish_minute').onchange=new Function('checkFinish();');
	document.getElementById('event_date_finish_hour').onchange=new Function('checkFinish();');
}
