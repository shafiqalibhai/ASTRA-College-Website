<table width="100%" border="0" height="92%">
  <tr>
    <td width="24%" valign="top"><div class="containerFP">
        <h3><img src="images/group.gif" alt=" " width="13" height="13" align="absmiddle" /> College Community </h3>
        <br />
        <table border="0" cellpadding="0" cellspacing="0" width="70%" align="center">
          <tbody>
            <tr valign="top">
              <td width="3%"><img src="images/arc1.gif" height="21" width="23"></td>
              <td width="94%"><table border="0" cellpadding="0" cellspacing="0" width="100%">
                  <tbody>
                    <tr bgcolor="#d7d7d7" valign="top">
                      <td><img src="images/blank.gif" height="2" width="50"></td>
                    </tr>
                  </tbody>
                </table></td>
              <td align="right" width="3%"><img src="images/arc2.gif" height="21" width="22"></td>
            </tr>
            <tr valign="top">
              <td height="24" valign="bottom">&nbsp;</td>
              <td height="24" valign="bottom"><a href="interact/login.php" target="_blank">
                <dt>&nbsp;&nbsp;<img src="images/arrow.gif" alt=" " /> Login</dt>
                </a> <a href="interact/spaces/space.php?space_key=1" target="_blank">
                <dt>&nbsp;&nbsp;<img src="images/arrow.gif" alt=" " /> Browse</dt>
                </a> </td>
              <td align="right" height="24" valign="bottom">&nbsp;</td>
            </tr>
            <tr valign="top">
              <td height="24" valign="bottom" width="3%"><img src="images/arc3.gif" height="23" width="23"></td>
              <td height="24" valign="bottom" width="94%"><table border="0" cellpadding="0" cellspacing="0" width="100%">
                  <tbody>
                    <tr bgcolor="#d7d7d7" valign="top">
                      <td><img src="images/blank.gif" height="2" width="50"></td>
                    </tr>
                  </tbody>
                </table></td>
              <td align="right" height="24" valign="bottom" width="3%"><img src="images/arc4.gif" height="23" width="22"></td>
            </tr>
          </tbody>
        </table>
        <br />
      </div>
      <div class="containerFP" style="height:13%;">
        <h3><img src="images/statistics.gif" alt=" " width="13" height="13" align="absmiddle" /> Statistics</h3>
        <? include "interact/statisticsDisplay.php"; ?>
      </div>
      <div class="containerFP">
        <h3><img src="images/feed.gif" alt=" " width="13" height="13" align="absmiddle" /> Feeds</h3>
        <!--        <a href="interact/spaces/rssFetchNewsopml.php" title="OPML" alt="OPML"><img src="images/opml.png" alt="RSS feeds" align="absmiddle" /> </a> 
-->
        <dt>
          <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
            <tbody>
              <tr>
                <td>&nbsp;</td>
                <td><a href="interact/spaces/rssFetchNews091.php" title="RSS 0.91" alt="RSS 0.91"><img src="images/rss091.gif" alt="RSS feeds" /></a></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td><a href="interact/spaces/rssFetchNews10.php" title="RSS 10" alt="RSS 10"><img src="images/rss10.gif" alt="RSS feeds" /></a></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td><a href="interact/spaces/rssFetchNews.php" title="RSS 20" alt="RSS 20"><img src="images/rss20.gif" alt="RSS feeds" /></a></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td><a href="interact/spaces/AtomFetchNews.php" title="ATOM 1.0" alt="ATOM 1.0"><img src="images/atom10.gif" alt="RSS feeds" /></a></td>
              </tr>
            </tbody>
          </table>
        </dt>
      </div>
      <div class="containerFP" align="left"> <a href="javascript:ajaxpage('content/contactAstraWebmasters.php','display');" name="webmasters" id="webmasters">
        <h3>
          <div title="ASTRA Webmasters"><img src="images/lightbulb.gif" alt=" " width="13" height="13" align="absmiddle" /> <u>Webmasters</u></div>
        </h3>
        </a> </div></td>
    <td width="51%" height="100%" valign="top"><div class="containerFP" style="height:99%;">
        <h3><img src="images/pin.gif" alt=" " width="13" height="13" align="absmiddle" /> College News </h3>
        <? include "interact/fetchNewsDisplay.php"; ?>
      </div></td>
    <td width="26%" valign="top" align="center"><div class="containerFP" style="height:280px;" align="left">
        <? include "content/myalbum/fs_auxDisplay.html"; ?>
      </div>
      <div class="containerFP" style="height:45px"> <br />
        <? include "libraries/msg.php"; ?>
      </div>
      <div class="containerFP" align="left">
        <h3><img src="images/weblink.gif" alt=" " width="13" height="13" align="absmiddle" /> Quick Links</h3>
        <ul>
          <li><a href="http://www.aurora.ac.in" title="Aurora Consortium">Aurora.ac.in</a></li>
          <li><a href="http://www.jntu.ac.in" title="Jawaharlal Nehru Technical University">JNTU.ac.in</a></li>
          <li><a href="http://www.indiaresults.com" title="External exam results can be found here if jntu site is down">Indiaresults.com</a></li>
          <li><a href="https://secure.classmates.com/cmo/login.jsp" title="Classmates.com">Classmates.com</a></li>
          <li><a href="http://www.facebook.com" title="For College/High School students and General">Facebook.com</a></li>
          <li><a href="http://www.graduates.com/login_form.asp" title="For School, college, and work">Graduates.com</a></li>
          <li><a href="http://www.librarything.com" title="For Book lovers">LibraryThing.com</a></li>
          <li><a href="http://www.meebo.com" title="Online Chatting Without Messenger">Meebo.com</a> </li>
        </ul>
      </div></td>
  </tr>
</table>
