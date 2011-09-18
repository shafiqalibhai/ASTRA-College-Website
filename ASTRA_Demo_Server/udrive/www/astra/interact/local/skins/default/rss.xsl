<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="1.1" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

  <xsl:output method="html" /> 
	
	 <xsl:variable name="title" select="/rss/channel/title"/>		
	<xsl:template match="/">
	
		<html>
			<head>
				<title>
					<xsl:value-of select="$title"/> XML Feed</title>
						
			</head>	
		<xsl:apply-templates select="rss/channel"/>		
		</html>
	</xsl:template>
	
		<xsl:template match="channel">
		<body>		
					
		 	<div class="topbox">
			<div class="padtopbox">
			<h2>What is this page?</h2>
			<p>This is an RSS feed from the <xsl:value-of select="image/title" /> website. RSS feeds allow you to stay up to date with the latest news and features from this site.</p>
			<p>To subscribe to it, you will need a News Reader or other similar device. If you would like to use this feed to display  <xsl:value-of select="image/title" /> content on your site, 
			
			</p>
			
			</div>
			</div>		
			
			<div class="banbox">
			<div class="padbanbox">			
			<div class="mvb">
			<div class="fltclear">Below is the latest content available from this feed. 			
			
			</div>		
			<div class="fltl"><span class="subhead"></span></div><a href="#" class="item"><img height="15" hspace="5" vspace="0" border="0" width="32" alt="RSS" src="../../images/rss_feed.gif" title="RSS" align="left" /><xsl:value-of select="$title"/></a><br clear="all" />
			 </div>
			 
			
			
	
			</div>
			</div>		
			
			<div class="mainbox">
				<div class="itembox">
					<div class="paditembox">
					<xsl:apply-templates select="item"/>
					</div>
				</div>	
				<div class="rhsbox">
					<div class="padrhsbox">
					<h2>Subscribe to this feed</h2>
					<p>You can subscribe to this RSS feed in a number of ways, including the following:</p>
					<ul>
					<li>Drag the orange RSS button into your News Reader</li>
					<li>Drag the URL of the RSS feed into your News Reader</li>
					<li>Cut and paste the URL of the RSS feed into your News Reader</li>
					</ul>										
					<xsl:if test="system-property('xsl:vendor')='Transformiix'">					
					</xsl:if>
					<xsl:if test="system-property('xsl:vendor')='Microsoft'">
					<div class="mvb">
					<span class="subhead">One-click subscriptions</span>
					</div>
					<div class="mvb">
					If you use one of the following web-based News Readers, click on the appropriate button to subscribe to the RSS feed.
					</div>
					
					<script language="javascript" type="text/javascript"> 									var url=window.location;
  	document.write('<a href="http://add.my.yahoo.com/rss?url='+ url +'"><img height="17" width="91" vspace="3" border="0" alt="my yahoo" src="http://newsimg.bbc.co.uk/shared/bsp/xsl/rss/img/myyahoo.gif" /></a><br />');
	document.write('<a href="http://www.bloglines.com/sub/'+ url +'"><img height="18" width="91" vspace="3" border="0" alt="bloglines" src="http://newsimg.bbc.co.uk/shared/bsp/xsl/rss/img/bloglines.gif" /></a><br />');
	document.write('<a href="http://www.newsgator.com/ngs/subscriber/subext.aspx?url='+ url +'"><img height="17" width="91" vspace="3" border="0" alt="newsgator" src="http://newsimg.bbc.co.uk/shared/bsp/xsl/rss/img/newsgator.gif" /></a><br />');			
					</script>	
					
					</xsl:if>			
							
					
					</div>		
				</div>	
			</div>	
			
		<div class="footerbox">
		
      		
		</div>
				
		</body>
	</xsl:template>
		
	<xsl:template match="item">
	<div id="item">
	<ul>
			<li>
				<a href="{link}" class="item">
					<xsl:value-of select="title"/>
				</a><br/>			
				<div>
				<xsl:value-of select="description" />					
				</div>	
				</li>		</ul>
	</div>		
	</xsl:template>
	
</xsl:stylesheet>