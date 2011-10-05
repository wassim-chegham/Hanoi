<?php
  error_reporting(E_ALL);
	//ob_start();
  
  define("OFFSET", 3);
  define("STRLEN", 23);
  
  define("DISC_SYMB", "&nbsp;"); //&#9679;
  define("STICK_SYMB", "|"); //"&brvbar;"
  define("BASE_SYMB", '&nbsp;');
  define("BASE_SYMB_SPLIT", '&nbsp;');
  
  $nb_coup = 0;

  $stack = array();
  extract($_POST);
  
  function init($nbr)
  {

    global $nb_taquets, $stack;
  
    // initialize stack with $nb_taquets stick and $nbr discs.
    for ($i = 1; $i <= $nb_taquets; $i++)
    {
    	$stack[ $i ] = array();
      for ($j = 1; $j <= $nbr; $j++)
    	{
   	 		$stack[ $i ][ $j ] = null;
    	}
    }	
    
    // draw the initial state 
    echo "<pre class='current-state' id='state-0'>";
    for( $i=1; $i<=$nbr+OFFSET; $i++ )
    {
      echo $i;
      for( $j=1; $j<=$nb_taquets; $j++ )
      {
        if ( $i > OFFSET &&  1 == $j && $i <= $nbr+OFFSET  ) 
        {
          $stack[ $j ][ $i-OFFSET ] = draw_disc_or_stick( $i-OFFSET );        
          echo $stack[ $j ][ $i-OFFSET ];
        }
      
        else echo draw_disc_or_stick(1, STICK_SYMB);

      }
      echo "\n";

    }
    
		echo print_base();
    echo "\n";
    if( isset($_POST['debug']) ) print_r($stack);
    echo "\n\n\n</pre>";
        
	}
  
  function hanoi($nbr, $dep, $fin, $int)
  {

    global $nb_coup;
    
    if($nbr > 0)
    {
      hanoi($nbr - 1, $dep, $int, $fin);
      
      draw_result($nb_coup, $nbr, $dep, $fin);
      $nb_coup++;
            
      hanoi($nbr - 1, $int, $fin, $dep);
    }

  }
  
  function push(&$from, &$to, $nb)
  {
  	global $nb_disques;
  	
  	$ii = $nb;
  	$item = $from[ $ii ];
  	while($item==null)
  	{ 
  		$item = $from[ ++$ii ];  		   
  	}
		$from[ $ii ] = null;

    $len = count($to);

    if( $len == 0 ) 
    	$to[0] = $item;
    else {
    	
    	$done = false;
    	for( $i=$nb_disques; !$done && $i>=1; $i-- )
    	{
		 		if( $to[ $i ] == null )
				{
		 			$to[ $i ] = $item;
		 			$done = true;
				}

    	}
	    
    }
    
  }
  
  //----
  function print_base()
  {
  	global $nb_taquets;
  	$m= floor( STRLEN/2 )+1;
  	$r = '';
  	for($i=1; $i<=$nb_taquets; $i++)
  	{
  		for($j=1; $j<=STRLEN; $j++) $r .= ( $j == $m ) ? BASE_SYMB_SPLIT : BASE_SYMB;
  	}
  	return '<span id="base">'.$r.'</span>';
  }
  
  function draw_disc_or_stick( $len, $symb=DISC_SYMB )
  {
    $d = array();
    
    for($i=1; $i<=STRLEN; $i++)
    {
      $mid = floor(STRLEN/2)+1; // 13
      if ( $mid-($len-1) <= $i  && $i <= $mid+($len-1) )
      {
        $d[ $i ] = '<b>'.$symb.'</b>';
      }
      else {
        $d[ $i ] = " ";
      }
    }
    
    return ($symb != DISC_SYMB ) ? '<span class="stick">'.implode('', $d).'</span>'
     : '<span style="width:'.($len*10).'px;" class="discs disc-'.$len.'">'.implode('', $d).'</span>';
  }
  
  function draw_result($i, $nbr, $dep, $fin)
  {

    global $nb_disques, $nb_taquets, $stack;
    echo "<pre class='display-none' id='state-" . ($i+1) . "'>";
    
    push($stack[ $dep ], $stack[ $fin ], $nbr);
		//ksort($stack[$dep]);
		//ksort($stack[$fin]);
    
    for ($i = 1; $i <= $nb_disques+OFFSET; $i++)
    {
    	echo $i;
      for ($j = 1; $j <= $nb_taquets; $j++)
      {
				if ( isset($stack[ $j ][ $i-OFFSET ]) ) 
          echo $stack[ $j ][ $i-OFFSET ];
          
        else
          //echo draw_disc_or_stick(1, $j);
          echo draw_disc_or_stick(1, STICK_SYMB);
      
      }
      echo "\n";
    }
    
    echo print_base();
    echo "\n>> Deplacer le disque ".($nbr+OFFSET)." depuis le taquet ".$dep." vers le taquet ".$fin."\n\n\n";
    if( isset($_POST['debug']) ) print_r($stack);
    echo "\n</pre>";
    //ob_end_flush();
  
  }


?>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script> 
<style>
   div#base
    {
			-moz-border-radius:5px;
      -webkit-border-radius:5px;
			-o-border-radius:5px;
      -khtml-border-radius:5px;
      background:black;
			height:20px;
    }
   span.discs {font-weight:bold;background-color:transparent;background-repeat:no-repeat;background-image:url('hanoi.png');}
   span.discs b {}

   span.stick {font-weight:bold;background:url("hanoi.png") no-repeat scroll center -96px transparent;}
   span.stick b {visibility:hidden;}

   span#base {background:#848484; -moz-border-radius:10px;}
   span.disc-1 {background-position:center -4px; color:#FE2EF7}
   span.disc-2 {background-position:center -21px; color:#00f;}
   span.disc-3 {background-position:center -39px; color:#0f0;}
   span.disc-4 {background-position:center -57px; color:#ff0;}
   span.disc-5 {background-position:center -75px; color:#f00;}
   
   pre {background-color:white;position:absolute;}
   
   .display-none {display:none;}

</style>
<h1>
	<span class="disc-5">H</span>
	<span class="disc-3">a</span>
	<span class="disc-2">n</span>
	<span class="disc-4">o</span>
	<span class="disc-1">i</span>
  <i>tower</i>
</h1>
<form method="post" action="" id="form">
  <p style="width:99%">
    <label for="nb_disques">NB Disques</label>
    <select name="nb_disques">
      <?php for($i=1; $i<=5; $i++): ?>
      <option value="<?php echo $i; ?>" <?php if( isset($nb_disques) && $nb_disques==$i) echo "selected='selected'"; ?> ><?php echo $i; ?></option>
      <?php endfor; ?>
    </select>

    <label for="nb_taquets">NB taquets</label>
    <select name="nb_taquets">
      <?php for($i=3; $i<=5; $i++): ?>
      <option value="<?php echo $i; ?>" <?php if( isset($nb_taquets) && $nb_taquets==$i) echo "selected='selected'"; ?> ><?php echo $i; ?></option>
      <?php endfor; ?>
    </select>
		
    <label for="intermediaire">intermediaire</label>
    <select name="intermediaire">
      <?php for($i=2; $i<=5; $i++): ?>
      <option value="<?php echo $i; ?>" <?php if( isset($intermediaire) && $intermediaire==$i) echo "selected='selected'"; ?> ><?php echo $i; ?></option>
      <?php endfor; ?>
    </select>
  
    <label for="fin">Arrivee</label>
    <select name="fin">
      <?php for($i=1; $i<=5; $i++): ?>
      <option value="<?php echo $i; ?>" <?php if( isset($fin) && $fin==$i) echo "selected='selected'"; ?> ><?php echo $i; ?></option>
      <?php endfor; ?>
    </select>

    <input type="submit" id="solve" name="solve" value="Solve" />
    <input type="button" id="prev" name="prev" value="Previous" disabled="disabled"/>
    <input type="button" id="next" name="next" value="next" disabled="disabled"/>

    <label for="debut">Debug</label>
    <input type="checkbox" name="debug" value="true" <?php if( isset($debug) ) echo "checked='checked'";?>/>
    
  </p>
</form>
<hr />
<?php

  if( isset($nb_disques) ) init($nb_disques);
  
  if ( isset($solve) ) 
  {
    
    $debut = 1;
    
    if ( $debut == $fin )
    {
      exit("<script>alert('ATTENTION: Le taquet de depart doit etre different du taquet d\'arrivee');</script>");
    }
    else if ( $debut == $intermediaire )
    {
      exit("<script>alert('ATTENTION: Le taquet de depart doit etre different du taquet intermediaire');</script>");
    }
    else if ( $fin == $intermediaire )
    {
      exit("<script>alert('ATTENTION: Le taquet d\'arrivee doit etre different du taquet intermediaire');</script>");
    }
    else if ( $intermediaire > $nb_taquets )
    {
      exit("<script>alert('ATTENTION: Le taquet intermediaire n\'est pas valide');</script>");
    }    
    else if ( $fin > $nb_taquets )
    {
      exit("<script>alert('ATTENTION: Le taquet d\'arrivee n\'est pas valide');</script>");
    }    
    else {
    	hanoi( $nb_disques, 1, $fin, $intermediaire);
		  echo '<script>
        
        function next()
        {
          if ( $("pre.current-state").next("pre").length != 0 )
          {
            $("pre.current-state")
              .fadeIn(500, function(){
                $(this)
                .removeClass("current-state")
                .addClass("display-none");
              })
              .next("pre")
              .fadeOut(500, function(){
                $(this)
                .addClass("current-state")
                .removeClass("display-none");
              });
            
            $("#prev").attr("disabled", false);
          }
          else {
            $("#next").attr("disabled", true);
          }
        }
        
        function prev()
        {
          if ( $("pre.current-state").prev("pre").length != 0 )
          {
            $("pre.current-state")
              .fadeIn(500, function(){
                $(this)
                .removeClass("current-state")
                .addClass("display-none");
              })
              .prev("pre")
              .fadeOut(500, function(){
                $(this)
                .addClass("current-state")
                .removeClass("display-none");
              });

            $("#next").attr("disabled", false);

          }
          else {
            $("#prev").attr("disabled", true);
          }
        }
        
        function init()
        {
          $("#prev").attr("disabled", false);
          $("#next").attr("disabled", false);
        }
        
        $(function(){
          alert("Accomplis en '.$nb_coup.' coups");
          init();
          $("#prev").click(function(){ prev(); });
          $("#next").click(function(){ next(); });
        });
        
      </script>';
    }

  }

?>
