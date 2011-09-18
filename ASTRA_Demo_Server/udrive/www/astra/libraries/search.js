      function OnLoad() {
       
        
        var tabbed = new GSearchControl();
        
        tabbed.setLinkTarget(GSearch.LINK_TARGET_BLANK );
        tabbed.addSearcher(new GwebSearch());
        tabbed.addSearcher(new GlocalSearch());
        tabbed.addSearcher(new GblogSearch());
        tabbed.addSearcher(new GnewsSearch());
        tabbed.addSearcher(new GbookSearch());
		tabbed.addSearcher(new GvideoSearch());
       
        var cseId = "017576662512468239146:omuauf_lfve";
        var searcher;
        var options;
        
        searcher = new GwebSearch();
        options = new GsearcherOptions();
        searcher.setSiteRestriction("000455696194071821846:comparisons");
        searcher.setUserDefinedLabel("Prices");
        tabbed.addSearcher(searcher, options);

        searcher = new GwebSearch();
        options = new GsearcherOptions();
        searcher.setSiteRestriction("000455696194071821846:community");
        searcher.setUserDefinedLabel("Forums");
        tabbed.addSearcher(searcher, options);

        searcher = new GwebSearch();
        options = new GsearcherOptions();
        searcher.setSiteRestriction("000455696194071821846:shopping");
        searcher.setUserDefinedLabel("Shopping");
        tabbed.addSearcher(searcher, options);
		
		searcher = new GwebSearch();
        options = new GsearcherOptions();
        searcher.setSiteRestriction(cseId, "Lectures");
        searcher.setUserDefinedLabel("Lectures");
        tabbed.addSearcher(searcher, options);

        searcher = new GwebSearch();
        options = new GsearcherOptions();
        searcher.setSiteRestriction(cseId, "Assignments");
        searcher.setUserDefinedLabel("Assignments");
        tabbed.addSearcher(searcher, options);

        searcher = new GwebSearch();
        options = new GsearcherOptions();
        searcher.setSiteRestriction(cseId, "Reference");
        searcher.setUserDefinedLabel("Reference");
        tabbed.addSearcher(searcher, options);
        
        
        var drawOptions = new GdrawOptions();
        drawOptions.setDrawMode(GSearchControl.DRAW_MODE_TABBED);
        tabbed.draw(document.getElementById("search_control_tabbed"), drawOptions);


            
            
        tabbed.execute("");
      }
      GSearch.setOnLoadCallback(OnLoad);
    