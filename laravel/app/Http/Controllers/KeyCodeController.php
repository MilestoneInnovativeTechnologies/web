<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KeyCodeController extends Controller
{
	
	public function KeyEncode($PArray, $VArray){
		return $this->Encode($PArray, $VArray);
	}
	
	public function KeyDecode($Key){
		return $this->Decode($Key);
	}
	
	public function test(Request $request){
		if($request->k) return $this->Decode($request->k);
		$All = $request->all();
		$Keys = array_keys($All); $Vals = array_values($All);
		return $this->Encode($Keys,$Vals);
		$key = [];
		for($i=0; $i<100; $i++) $key[] = $this->KeyEncode($Keys,$Vals);
		return $key;
		$key = $this->Encode($Keys,$Vals);
		$Dec = $this->Decode($key);
	}
	
	static function Encode($PArray, $VArray){
		$A = base64_encode(implode("|",$PArray));
		$B = base64_encode(implode("|",$VArray));
		$C = mt_rand(1,5);
		$D = str_split($A,$C); $F = count($D); $D[$F-1] = str_pad(end($D),$C,"$");
		$E = str_split($B,$C); $G = count($E); $E[$G-1] = str_pad(end($E),$C,"$");
		$H = max($F,$G);
		$J = "";
		for($I=0; $I<$H; $I++){
			if($F>$I) $J .= $D[$I];
			if($G>$I) $J .= $E[$I];
		}
		$K = mb_strlen($J); $L = (intval($K/3)<15)?(intval($K/3)):11; $M = dechex($L);
		$N = str_split($J,$L);
		$O = dechex($F);
		$P = dechex($G);
		$Q = [$M,$N[0],$C,$N[1],$O,"g",join("",array_slice($N,2)),"h",$P];
		$R = join("",$Q);
		//return [$A,$B,$C,$D,$E,$F,$G,$H,$I,$J,$K,$L,$M,$N,$O,$P,$Q,$R];
		return $R;
	}
	
	static function Decode($Key){
		$T = 0;
		$M = mb_substr($Key,$T,1); $T += 1;
		$L = hexdec($M);
		$N = [];
		$N[0] = mb_substr($Key,$T,$L); $T += $L;
		$C = mb_substr($Key,$T,1); $T += 1;
		$N[1] = mb_substr($Key,$T,$L); $T += $L;
		$U = mb_strpos(mb_substr($Key,$T),"g");
		$O = mb_substr($Key,$T,$U); $T += $U; $T++;
		$F = hexdec($O);
		$P = mb_substr(strrchr($Key,"h"),1);
		$G = hexdec($P);
		$N[2] = mb_substr(mb_substr($Key,$T),0,(0-(1+mb_strlen($P))));
		$J = join("",$N);
		$V = str_split($J,($C*2));
		$D = []; $E = [];
		foreach($V as $I => $W){
			$D1 = mb_substr($W,0,$C); $E1 = mb_substr($W,$C);
			if($F > $I && $G > $I) { $D[] = $D1; $E[] = $E1; continue; }
			if($F > $I) { $D[] = $D1; if($E1 != "") $D[] = $E1; continue; }
			if($G > $I) { $E[] = $D1; if($E1 != "") $E[] = $E1; continue; }
		}
		$D[$F-1] = str_replace("$","",end($D));
		$E[$G-1] = str_replace("$","",end($E));
		$A = join("",$D); $B = join("",$E);
		//return [$Key,$M,$L,$N[0],$C,$N[1],$U,$O,$F,$P,$G,$N[2],$J,$V,$D,$E,$A,$B];
		return[explode("|",base64_decode($A)),explode("|",base64_decode($B))];
	}

}