function mmLoadMenus() {
  if (window.about) return;
 
 window.about = new Menu("root",165,19,"Arial",10,"#FFFFFF","#FFFFFF","#125595","#024079","left","middle",3,1,1000,-5,7,true,false,true,0,true,false);
   about.addMenuItem("Founder / Chairman","javascript:ajaxpage('content/founderChairman.php','display');");
   about.addMenuItem("Director","javascript:ajaxpage('content/director.php','display');");
   about.addMenuItem("History","javascript:ajaxpage('content/history.php','display');");
   about.addMenuItem("College","javascript:ajaxpage('content/college.php','display');");
   about.addMenuItem("Festivals","javascript:ajaxpage('content/festivals.php','display');");
   about.addMenuItem("Rules & Regulations","javascript:ajaxpage('content/rulesRegulations.php','display');");
   about.addMenuItem("Code of Conduct","javascript:ajaxpage('content/codeOfConduct.php','display');");
   about.addMenuItem("Student Matters","javascript:ajaxpage('content/studentMatters.php','display');");
   about.addMenuItem("Student Activities","javascript:ajaxpage('content/studentActivities.php','display');");
   about.addMenuItem("Centres of Excellence","javascript:ajaxpage('content/centresOfExcellence.php','display');");
/*   about.addMenuItem("Religious Festivals","javascript:ajaxpage('content/impReligiousFestivals.php','display');");
*/
 window.academics = new Menu("root",140,19,"Arial",10,"#FFFFFF","#FFFFFF","#125595","#024079","left","middle",3,1,1000,-5,7,true,false,true,0,true,false);
   academics.addMenuItem("Mission and Vision","javascript:ajaxpage('content/missionVision1.php','display');");
   academics.addMenuItem("Overview","javascript:ajaxpage('content/overview.php','display');");
   academics.addMenuItem("Methodology","javascript:ajaxpage('content/methodology.php','display');");
   academics.addMenuItem("Courses Offered","javascript:ajaxpage('content/coursesOffered.php','display');");
   academics.addMenuItem("Support Systems","javascript:ajaxpage('content/supportSystems.php','display');");
   academics.addMenuItem("Placements","javascript:ajaxpage('content/placements.php','display');");
   academics.addMenuItem("Teaching-Learning Process","javascript:ajaxpage('content/teachingLearningProcess.php','display');");

 
 window.departments = new Menu("root",100,19,"Arial",10,"#FFFFFF","#FFFFFF","#125595","#024079","left","middle",3,1,1000,-5,7,true,false,true,0,true,false);
   departments.addMenuItem("CSE","javascript:ajaxpage('content/cseDisplay.php','display');");
   departments.addMenuItem("IT","javascript:ajaxpage('content/itDisplay.php','display');");
   departments.addMenuItem("EEE","javascript:ajaxpage('content/eeeDisplay.php','display');");
   departments.addMenuItem("ECE","javascript:ajaxpage('content/eceDisplay.php','display');");
   departments.addMenuItem("MCA","javascript:ajaxpage('content/mcaDisplay.php','display');");
   departments.addMenuItem("MBA","javascript:ajaxpage('content/mbaDisplay.php','display');");
   departments.addMenuItem("H & Sc","javascript:ajaxpage('content/h&ScDisplay.php','display');");
   
 window.student = new Menu("root",100,19,"Arial",10,"#FFFFFF","#FFFFFF","#125595","#024079","left","middle",3,1,1000,-5,7,true,false,true,0,true,false);
   student.addMenuItem("IEEE","javascript:ajaxpage('content/ieee.php','display');");
   student.addMenuItem("ACM","javascript:ajaxpage('content/acm.php','display');");


about.writeMenus();
} 