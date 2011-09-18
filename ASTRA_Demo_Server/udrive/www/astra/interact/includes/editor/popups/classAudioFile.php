<?php

// Incorporated into Interact 2007-04-13.  Most non-mp3 stuff removed.

// ************************************************************************
// Class      AudioFile
// Version:   0.5.1
// Date:      09/09/2003
// Author:    michael kamleitner (mika@ssw.co.at)
//	      reto gassmann (gassi@gassi.cx) - additional mp3-code
//	      chris snyder (csnyder@chxo.com) - additional ogg-vorbis & id3v2-code	  
// WWW:	      http://www.entropy.at/forum.php?action=thread&t_id=15
//            (suggestions, bug-reports & general shouts are welcome)
// Copyright: copyright 2003 michael kamleitner, reto gassmann, chris snyder
//
//            This file is part of classAudioFile.
//
//            classAudioFile is free software; you can redistribute it and/or modify
//            it under the terms of the GNU General Public License as published by
//            the Free Software Foundation; either version 2 of the License, or
//            (at your option) any later version.
//
//            classAudioFile is distributed in the hope that it will be useful,
//            but WITHOUT ANY WARRANTY; without even the implied warranty of
//            MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//            GNU General Public License for more details.
//
//            You should have received a copy of the GNU General Public License
//            along with classAudioFile; if not, write to the Free Software
//            Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
//            
// ************************************************************************

/* changes by Chris Snyder <csnyder@chxo.com>
	2003-08-01 -- rough draft
	
	Expanded ID3V2 support:
	- function getId3v2 ($fp) -- reads all id3v2 data into $this->id3v2 and positions filepointer at end of header
	- function get32bitSynchsafe($fp) -- reads next four bytes from $fp as a Synchsafe integer
	- added id3v2 to printSampleInfo
	
	Added Ogg Vorbis support:
	- function ogginfo($fp) -- similar to mp3info()
	- function findVorbis($fp) -- seeks to start of Vorbis headers in Ogg Bitstream
	- function findOggPage($fp) -- seeks to start of next logical page of Ogg Bitstream
	- added ogginfo to printSampleInfo

	Miscellaneous Bugfixes:
	- line 143: added rtrim to $info["mpeg_id3v1_tag"] values to suppress padding bytes
	- line 283: if duration is unknown because mp3 is vbr, check if there's an id3v2 TLEN frame
	- line 418: revised visualization data-collection loop to only read bytes needed for visual_data -- script was timing out on wavs larger than 10MB
*/

class AudioFile
{
	var $wave_id;
	var $wave_type;
	var $wave_compression;
	var $wave_channels;
	var $wave_framerate;
	var $wave_byterate;
	var $wave_bits;
	var $wave_size;
	var $wave_filename;
	var $wave_length;
	
	var $id3_tag;
	var $id3_title;
	var $id3_artist;
	var $id3_album;
	var $id3_year;
	var $id3_comment;
	var $id3_genre;

	var $id3v2info;
	
	var $visual_graph_color;	// HTML-Style: "#rrggbb"
	var $visual_background_color;
	var $visual_grid_color;
	var $visual_border_color;
	var $visual_grid;		// true/false
	var $visual_border;		// true/false
	var $visual_width;		// width in pixel
	var $visual_height;		// height in pixel
	var $visual_graph_mode;		// 0|1
	var $visual_fileformat;		// "jpeg","png", everything & else default = "png"

	// ************************************************************************
	// mp3info extracts the attributes of mp3-files
	// (code contributed by reto gassmann (gassi@gassi.cx)
	// ************************************************************************			
	
	function mp3info()
	{
		$byte 			= array();
		$version 		= array("MPEG Version 2.5",false,"MPEG Version 2 (ISO/IEC 13818-3)","MPEG Version 1 (ISO/IEC 11172-3)");
		$version_bitrate	= array(1,false,1,0);
		$version_sampling	= array(2,false,1,0);
		$layer			= array(false,"Layer III","Layer II","Layer I");
		$layer_bitrate		= array(false,2,1,0);
		$layer_lengt		= array(false,1,1,0);
		$protection 		= array("Protected by CRC (16bit crc follows header)","Not protected");
		$byterate		= array(
						array(
							array("free",32,64,96,128,160,192,224,256,288,320,352,384,416,448,"bad"),
							array("free",32,48,56, 64, 80, 96,112,128,160,192,224,256,320,384,"bad"),
							array("free",32,40,48, 56, 64, 80, 96,112,128,160,192,224,256,320,"bad")
						     ),
						array(
							array("free",32,48,56, 64, 80, 96,112,128,144,160,176,192,224,256,"bad"),
							array("free", 8,16,24, 32, 40, 48, 56, 64, 80, 96,112,128,144,160,"bad"),
							array("free", 8,16,24, 32, 40, 48, 56, 64, 80, 96,112,128,144,160,"bad")
						     )
					       );
		$samplingrate		= array(
						array(44100,48000,32000,false),
						array(22050,24000,16000,false),
						array(11025,12000, 8000,false)
					       );
		$cannel_mode	= array("Stereo","Joint stereo (Stereo)","Dual channel (Stereo)","Single channel (Mono)");
		$copyright	= array("Audio is not copyrighted","Audio is copyrighted ");
		$original	= array("Copy of original media","Original media"); 
		$emphasis	= array("none","50/15 ms",false,"CCIT J.17 ");
	
	//id3-stuff
	
	$genre			= array
					("Blues","Classic Rock","Country","Dance","Disco","Funk","Grunge","Hip-Hop","Jazz","Metal","New Age","Oldies","Other","Pop","R&B",
					"Rap","Reggae","Rock","Techno","Industrial","Alternative","Ska","Death Metal","Pranks","Soundtrack","Euro-Techno","Ambient","Trip-Hop",
					"Vocal","Jazz+Funk","Fusion","Trance","Classical","Instrumental","Acid","House","Game","Sound Clip","Gospel","Noise","Alternative Rock",
					"Bass","Soul","Punk","Space","Meditative","Instrumental Pop","Instrumental Rock","Ethnic","Gothic","Darkwave","Techno-Industrial",
					"Electronic","Pop-Folk","Eurodance","Dream","Southern Rock","Comedy","Cult","Gangsta","Top 40","Christian Rap","Pop/Funk","Jungle",
					"Native US","Cabaret","New Wave","Psychadelic","Rave","Showtunes","Trailer","Lo-Fi","Tribal","Acid Punk","Acid Jazz","Polka","Retro",
					"Musical","Rock & Roll","Hard Rock","Folk","Folk-Rock","National Folk","Swing","Fast Fusion","Bebob","Latin","Revival","Celtic","Bluegrass",
					"Avantgarde","Gothic Rock","Progressive Rock","Psychedelic Rock","Symphonic Rock","Slow Rock","Big Band","Chorus","Easy Listening","Acoustic",
					"Humour","Speech","Chanson","Opera","Chamber Music","Sonata","Symphony","Booty Bass","Primus","Porn Groove","Satire","Slow Jam","Club",
					"Tango","Samba","Folklore","Ballad","Power Ballad","Rhytmic Soul","Freestyle","Duet","Punk Rock","Drum Solo","Acapella","Euro-House",
					"Dance Hall","Goa","Drum & Bass","Club-House","Hardcore","Terror","Indie","BritPop","Negerpunk","Polsk Punk","Beat","Christian Gangsta Rap",
					"Heavy Metal","Black Metal","Crossover","Contemporary Christian","Christian Rock","Merengue","Salsa","Trash Metal","Anime","Jpop","Synthpop");
	
	//id3v2 check----------------------------
		$footer = 0;
		$header = 0;
		$v1tag	= 0;
		$fp = fopen($this->wave_filename,"r");
		$tmp = fread($fp,3);
		if($tmp == "ID3")
		{
			// id3v2 tag is present
			$this->getId3v2($fp);
			
			// getId3v2 will position pointer at end of header
			$header= ftell($fp);

		} else {
			fseek ($fp,0);
			$this->id3v2 = false;
		}

		for ($x=0;$x<4;$x++)
		{
			$byte[$x] = ord(fread($fp,1));
		}
		fseek ($fp, -128 ,SEEK_END);
		$TAG = fread($fp,128);
		fclose($fp);

	//id tag?-------------------------------

		if(substr($TAG,0,3) == "TAG")
		{
			$v1tag = 128;
			$info["mpeg_id3v1_tag"]["title"] 	= rtrim(substr($TAG,3,30));
			$info["mpeg_id3v1_tag"]["artist"] 	= rtrim(substr($TAG,33,30));
			$info["mpeg_id3v1_tag"]["album"] 	= rtrim(substr($TAG,63,30));
			$info["mpeg_id3v1_tag"]["year"] 	= rtrim(substr($TAG,93,4));
			$info["mpeg_id3v1_tag"]["comment"] 	= rtrim(substr($TAG,97,30));
			$info["mpeg_id3v1_tag"]["genre"]	= "";
			$tmp = ord(substr($TAG,127,1));
			if($tmp < count($genre))
			{
				$info["mpeg_id3v1_tag"]["genre"] = $genre[$tmp];
			}
		} else {
			$info["mpeg_id3v1_tag"] = false;
		}
	
	//version-------------------------------
	
		$tmp = $byte[1] & 24;
		$tmp = $tmp >> 3;
		$info_i["mpeg_version"] = $tmp;
		$byte_v = $version_bitrate[$tmp];
		$byte_vs = $version_sampling[$tmp];
		$info["mpeg_version"] = $version[$tmp];
	
	//layer---------------------------------
	
		$tmp = $byte[1] & 6;
		$tmp = $tmp >> 1;
		$info_i["mpeg_layer"] = $tmp;
		$byte_l = $layer_bitrate[$tmp];
		$byte_len = $layer_lengt[$tmp];
		$info["mpeg_layer"] = $layer[$tmp];
	
	//bitrate-------------------------------
	
		$tmp = $byte[2] & 240;
		$tmp = $tmp >> 4;
		$info_i["mpeg_bitrate"] = $tmp;
		$info["mpeg_bitrate"] = $byterate[$byte_v][$byte_l][$tmp];
	
	//samplingrate--------------------------
	
		$tmp = $byte[2] & 12;
		$tmp = $tmp >> 2;
		$info["mpeg_sampling_rate"] = $samplingrate[$byte_vs][$tmp];
	
	//protection----------------------------
	
		$tmp = $byte[1] & 1;
		$info["mpeg_protection"] = $protection[$tmp];
		
	//paddingbit----------------------------
	
		$tmp = $byte[2] & 2;
		$tmp = $tmp >> 1;
		$byte_pad = $tmp;
		$info["mpeg_padding_bit"] = $tmp;
	
	//privatebit----------------------------
	
		$tmp = $byte[2] & 1;
		$byte_prv = $tmp;
	
	//channel_mode--------------------------
	
		$tmp = $byte[3] & 192;
		$tmp = $tmp >> 6;
		$info["mpeg_channel_mode"] = $cannel_mode[$tmp];
	
	//copyright-----------------------------
	
		$tmp = $byte[3] & 8;
		$tmp = $tmp >> 3;
		$info["mpeg_copyright"] = $copyright[$tmp];
	
	//original------------------------------
	
		$tmp = $byte[3] & 4;
		$tmp = $tmp >> 2;
		$info["mpeg_original"] = $original[$tmp];
		
	//emphasis------------------------------
	
		$tmp = $byte[3] & 3;
		$info["mpeg_emphasis"] = $emphasis[$tmp];
	
	//framelenght---------------------------
	
		if($info["mpeg_bitrate"] == 'free' or $info["mpeg_bitrate"] == 'bad' or 
		  !$info["mpeg_bitrate"] or !$info["mpeg_sampling_rate"]) 
		{
			$info["mpeg_framelength"] = 0;
		} else {
			if($byte_len == 0)
			{
				$rate_tmp = $info["mpeg_bitrate"] * 1000;
				$info["mpeg_framelength"] = (12 * $rate_tmp / $info["mpeg_sampling_rate"] + $byte_pad) * 4 ;
			} elseif($byte_len == 1) {
				$rate_tmp = $info["mpeg_bitrate"] * 1000;
				$info["mpeg_framelength"] = 144 * $rate_tmp / $info["mpeg_sampling_rate"] + $byte_pad;
			}
		}
		
	//duration------------------------------
	
		$tmp = filesize($this->wave_filename);
		$tmp = $tmp - $header - 4 - $v1tag;
		
		$tmp2 = 0;
		$info["mpeg_frames"]="";
		$info["mpeg_playtime"]="";
		if(!$info["mpeg_bitrate"] or $info["mpeg_bitrate"] == 'bad' or !$info["mpeg_sampling_rate"]) 
		{
			$info["mpeg_playtime"] = -1;
		} elseif($info["mpeg_bitrate"] == 'free') 
		{	
			$info["mpeg_playtime"] = -1;
		} else {
			$tmp2 = ((8 * $tmp) / 1000) / $info["mpeg_bitrate"];
			$info["mpeg_frames"] = floor($tmp/$info["mpeg_framelength"]);
			$tmp = $tmp * 8;
			if ($rate_tmp<>0)
			{
				$info["mpeg_playtime"] = $tmp/$rate_tmp;
			}
			$info["mpeg_playtime"] = $tmp2;
		}
	
		// transfer the extracted data into classAudioFile-structure

		$this->wave_id = "MPEG";
		$this->wave_type = $info["mpeg_version"];
		$this->wave_compression = $info["mpeg_layer"];
		$this->wave_channels = $info["mpeg_channel_mode"];
		$this->wave_framerate = $info["mpeg_sampling_rate"];
		$this->wave_byterate = $info["mpeg_bitrate"] . " Kbit/sec";
		$this->wave_bits = "n/a";
		$this->wave_size = filesize($this->wave_filename);
		$this->wave_length = $info["mpeg_playtime"];
		
		// pick up length from id3v2 tag if necessary and available
		if ($this->wave_length<1 && is_array($this->id3v2->TLEN) )
		{
			$this->wave_length= (  $this->id3v2->TLEN['value'] / 1000 );
		}
		
		$this->id3_tag = $info["mpeg_id3v1_tag"];
		
		if ($this->id3_tag)
		{
			$this->id3_title = $info["mpeg_id3v1_tag"]["title"];
			$this->id3_artist = $info["mpeg_id3v1_tag"]["artist"];
			$this->id3_album = $info["mpeg_id3v1_tag"]["album"];
			$this->id3_year = $info["mpeg_id3v1_tag"]["year"];
			$this->id3_comment = $info["mpeg_id3v1_tag"]["comment"];
			$this->id3_genre = $info["mpeg_id3v1_tag"]["genre"];
		}
	}

	// ************************************************************************
	// longCalc calculates the decimal value of 4 bytes
	// mode = 0 ... b1 is the byte with least value
	// mode = 1 ... b1 is the byte with most value
	// ************************************************************************			

	function longCalc ($b1,$b2,$b3,$b4,$mode)
	{
		$b1 = hexdec(bin2hex($b1));    					
		$b2 = hexdec(bin2hex($b2));    					
		$b3 = hexdec(bin2hex($b3));    					
		$b4 = hexdec(bin2hex($b4));    					
		if ($mode == 0)
		{
			return ($b1 + ($b2<<8) + ($b3 <<16) + ($b4 <<24));	
		} else {
			return ($b4 + ($b3<<8) + ($b2 <<16) + ($b1 <<24));
		}
	}

	// ************************************************************************
	// shortCalc calculates the decimal value of 2 bytes
	// mode = 0 ... b1 is the byte with least value
	// mode = 1 ... b1 is the byte with most value
	// ************************************************************************			

	function shortCalc ($b1,$b2,$mode)
	{
		$b1 = hexdec(bin2hex($b1));    					
		$b2 = hexdec(bin2hex($b2));    					
		if ($mode == 0)
		{
			return ($b1 + ($b2<<8));	
		} else {
			return ($b2 + ($b1<<8));
		}
	}
	
	// ************************************************************************
	// getCompression delivers a string which identifies the compression-mode 
	// of the AudioFile-Object 
	// ************************************************************************
	
	function getCompression ($id)
	{
		return ($id);	
	}
	

	
	// ************************************************************************
	// getSampleInfo extracts the attributes of the AudioFile-Object
	// ************************************************************************			

	function getSampleInfo ()
	{				
		$valid = true;

		if (strstr(strtoupper($this->wave_filename),"MP3"))
		{
			$this->mp3info ();
		} else {$valid=false;}

		return ($valid);
	}

	// ************************************************************************
	// printSampleInfo prints the attributes of the AudioFile-Object
	// ************************************************************************
	
	function printSampleInfo()
	{	
		print "<table width=100% border=1>";
		print "<tr><td align=right>filename</td>		<td>&nbsp;$this->wave_filename</td></tr>";
		print "<tr><td align=right>id</td>		<td>&nbsp;$this->wave_id</td></tr>";
		print "<tr><td align=right>type</td>	<td>&nbsp;$this->wave_type</td></tr>";
		print "<tr><td align=right>size</td>	<td>&nbsp;$this->wave_size</td></tr>";
		print "<tr><td align=right>compression</td>	<td>&nbsp;".$this->getCompression ($this->wave_compression)."</td></tr>";
		print "<tr><td align=right>channels</td>	<td>&nbsp;$this->wave_channels</td></tr>";
		print "<tr><td align=right>framerate</td>	<td>&nbsp;$this->wave_framerate</td></tr>";
		print "<tr><td align=right>byterate</td>	<td>&nbsp;$this->wave_byterate</td></tr>";
		print "<tr><td align=right>bits</td>	<td>&nbsp;$this->wave_bits</td></tr>";
		print "<tr><td align=right>length</td>	<td>&nbsp;".number_format ($this->wave_length,"2")." sec.<br>&nbsp;".date("i:s", mktime(0,0,round($this->wave_length)))."</td></tr>";

		print "</table>";
	}

	// ************************************************************************
	// loadFile initializes the AudioFile-Object
	// ************************************************************************		
		
	function loadFile ($loadFilename)
	{
		$this->wave_filename = $loadFilename;
		$this->getSampleInfo ();
		$this->visual_graph_color = "#18F3AD";
		$this->visual_background_color = "#000000";
		$this->visual_grid_color = "#002C4A";
		$this->visual_border_color = "#A52421";
		$this->visual_grid = true;
		$this->visual_border = true;
		$this->visual_width = 600;
		$this->visual_height = 512;
		$this->visual_graph_mode = 1;
		$this->visual_fileformat = "png";
	}
	
	
	// ************************************************************************
	// getId3v2 loads id3v2 frames into $this->id3v2-><frameid>
	//	- any frame flags are saved in an array called <frameid>_flags
	//	- for instance, song title will be in $this->id3v2->TIT2
	//	   and any flags set in TIT2 would be in array $this->id3v2->TIT2_flags
	//
	// For common frame id codes see http://www.id3.org/id3v2.4.0-frames.txt
	// For more info on format see http://www.id3.org/id3v2.4.0-structure.txt
	// ************************************************************************

	function getId3v2 (&$fp)
	{
		// ID3v2 version 4 support -- see http://www.id3.org/id3v2.4.0-structure.txt
		$footer = 0;

		// id3v2 version
		$tmp = ord(fread($fp,1));
		$tmp2 = ord(fread($fp,1));
		$this->id3v2->version = "ID3v2.".$tmp.".".$tmp2;

		// flags
		$tmp = ord(fread($fp,1));
		if($tmp & 128) $this->id3v2->unsynch = "1";
		if($tmp & 64) $this->id3v2->extended = "1";
		if($tmp & 32) $this->id3v2->experimental = "1";
		if($tmp & 16)
		{
			$this->id3v2->footer = "1";
			$footer = 10;
		}
		
		// tag size
		$tagsize = $this->get32bitSynchsafe($fp) + $footer;
		
		// extended header
		if (isset($this->id3v2->extended) && $this->id3v2->extended==1)
		{
			// get extended header size
			$extended_header_size = $this->get32bitSynchsafe($fp) ;
			
			// load (but ignore) extended header
			$this->id3v2->extended_header= fread($fp, $extended_header_size);
		}
		
		// get the tag contents
		while ( ftell($fp) < ($tagsize+10) )
		{
			// get next frame header
			$frameid = fread($fp,4);
			if (trim($frameid)=="") break;
			$framesize= $this->get32bitSynchsafe($fp);
			$frameflags0= ord(fread($fp,1));
			$frameflags1= ord(fread($fp,1));

			// frame status flags
			$frameidflags= $frameid."_flags";
			if ($frameflags0 & 128) $this->id3v2->{$frameidflags}['tag_alter_discard'] = 1;
			if ($frameflags0 & 64) $this->id3v2->{$frameidflags}['file_alter_discard'] = 1;
			if ($frameflags0 & 32) $this->id3v2->{$frameidflags}['readonly'] = 1;

			// frame format flags
			if ($frameflags1 & 128) $this->id3v2->{$frameidflags}['group'] = 1;
			if ($frameflags1 & 16) $this->id3v2->{$frameidflags}['compressed'] = 1;
			if ($frameflags1 & 8) $this->id3v2->{$frameidflags}['encrypted'] = 1;
			if ($frameflags1 & 4) $this->id3v2->{$frameidflags}['unsyrchronised'] = 1;
			if ($frameflags1 & 2) $this->id3v2->{$frameidflags}['data_length_indicator'] = 1;

			// get frame contents
			$this->id3v2->{$frameid} = trim(fread($fp, $framesize));
		}

		// position $fp at end of id3v2header
		fseek($fp, ($tagsize + 10), SEEK_SET);
		return 1;
	}

	
	// ************************************************************************
	// get32bitSynchsafe returns a converted integer from an ID3v2 tag
	// ************************************************************************

	function get32bitSynchsafe(&$fp)
	{
		/* Synchsafe integers are
		integers that keep its highest bit (bit 7) zeroed, making seven bits
		out of eight available. Thus a 32 bit synchsafe integer can store 28
		bits of information.
		*/
		$tmp = ord(fread($fp,1)) & 127;
		$tmp2 = ord(fread($fp,1)) & 127;
		$tmp3 = ord(fread($fp,1)) & 127;
		$tmp4 = ord(fread($fp,1)) & 127;
		$converted = ($tmp * 2097152) + ($tmp2 * 16384) + ($tmp3 * 128) + $tmp4;
		return $converted;
	}
	

}

?>

