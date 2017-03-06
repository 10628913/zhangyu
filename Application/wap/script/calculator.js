try {  
	document.execCommand("BackgroundImageCache", false, true); 
} catch(err) {}
//62.180.9

//定义利率
lilv_array=new Array;          
//2004年之前的旧利率
lilv_array[1]=new Array;
lilv_array[1][1]=new Array;
lilv_array[1][2]=new Array;
lilv_array[1][1][5]=0.0477;//商贷 1～5年 4.77%
lilv_array[1][1][10]=0.0504;//商贷 5-30年 5.04%
lilv_array[1][2][5]=0.0360;//公积金 1～5年 3.60%
lilv_array[1][2][10]=0.0405;//公积金 5-30年 4.05%

//2005年	1月的新利率
lilv_array[2]=new Array;
lilv_array[2][1]=new Array;
lilv_array[2][2]=new Array;
lilv_array[2][1][5]=0.0495;//商贷 1～5年 4.95%
lilv_array[2][1][10]=0.0531;//商贷 5-30年 5.31%
lilv_array[2][2][5]=0.0378;//公积金 1～5年 3.78%
lilv_array[2][2][10]=0.0423;//公积金 5-30年 4.23%

//2006年	1月的新利率下限
lilv_array[3]=new Array;
lilv_array[3][1]=new Array;
lilv_array[3][2]=new Array;
lilv_array[3][1][5]=0.0527;//商贷 1～5年 5.27%
lilv_array[3][1][10]=0.0551;//商贷 5-30年 5.51%
lilv_array[3][2][5]=0.0396;//公积金 1～5年 3.96%
lilv_array[3][2][10]=0.0441;//公积金 5-30年 4.41%
			
//2006年	1月的新利率上限
lilv_array[4]=new Array;
lilv_array[4][1]=new Array;
lilv_array[4][2]=new Array;
lilv_array[4][1][5]=0.0527;//商贷 1～5年 5.27%
lilv_array[4][1][10]=0.0612;//商贷 5-30年 6.12%
lilv_array[4][2][5]= 0.0396;//公积金 1～5年 3.96%
lilv_array[4][2][10]=0.0441;//公积金 5-30年 4.41%

//2006年	4月28日的新利率下限
lilv_array[5]=new Array;
lilv_array[5][1]=new Array;
lilv_array[5][2]=new Array;
lilv_array[5][1][5]=0.0551;//商贷 1～5年 5.51%
lilv_array[5][1][10]=0.0575;//商贷 5-30年 5.75%
lilv_array[5][2][5]= 0.0414;//公积金 1～5年 4.14%
lilv_array[5][2][10]=0.0459;//公积金 5-30年 4.59%

//2006年	4月28日的新利率上限
lilv_array[6]=new Array;
lilv_array[6][1]=new Array;
lilv_array[6][2]=new Array;
lilv_array[6][1][5]=0.0612;//商贷 1～5年 6.12%
lilv_array[6][1][10]=0.0639;//商贷 5-30年 6.39%
lilv_array[6][2][5]= 0.0414;//公积金 1～5年 4.14%
lilv_array[6][2][10]=0.0459;//公积金 5-30年 4.59%

//2006年	8月19日的新利率下限
lilv_array[7]=new Array;
lilv_array[7][1]=new Array;
lilv_array[7][2]=new Array;
lilv_array[7][1][5]=0.0551;//商贷 1～5年 5.51%
lilv_array[7][1][10]=0.0581;//商贷 5-30年 5.81%
lilv_array[7][2][5]= 0.0414;//公积金 1～5年 4.14%
lilv_array[7][2][10]=0.0459;//公积金 5-30年 4.59%

//2006年	8月19日的新利率上限
lilv_array[8]=new Array;
lilv_array[8][1]=new Array;
lilv_array[8][2]=new Array;
lilv_array[8][1][5]=0.0648;//商贷 1～5年 6.48%
lilv_array[8][1][10]=0.0684;//商贷 5-30年 6.84%
lilv_array[8][2][5]= 0.0414;//公积金 1～5年 4.14%
lilv_array[8][2][10]=0.0459;//公积金 5-30年 4.59%


//2007年	3月18日的新利率下限
lilv_array[9]=new Array;
lilv_array[9][1]=new Array;
lilv_array[9][2]=new Array;
lilv_array[9][1][5]=0.0574;//商贷 1～5年 5.74%
lilv_array[9][1][10]=0.0604;//商贷 5-30年 6.04%
lilv_array[9][2][5]=0.0432;//公积金 1～5年 4.32%
lilv_array[9][2][10]=0.0477;//公积金 5-30年 4.77%

//2007年	3月18日的新利率上限
lilv_array[10]=new Array;
lilv_array[10][1]=new Array;
lilv_array[10][2]=new Array;
lilv_array[10][1][5]=0.0675;//商贷 1～5年 6.75%
lilv_array[10][1][10]=0.0711;//商贷 5-30年 7.11%
lilv_array[10][2][5]=0.0432;//公积金 1～5年 4.32%
lilv_array[10][2][10]=0.0477;//公积金 5-30年 4.77%


//2007年	5月19日的新利率下限
lilv_array[11]=new Array;
lilv_array[11][1]=new Array;
lilv_array[11][2]=new Array;
lilv_array[11][1][5]=0.0589;//商贷 1～5年 5.89%
lilv_array[11][1][10]=0.0612;//商贷 5-30年 6.12%
lilv_array[11][2][5]=0.0441;//公积金 1～5年 4.41%%
lilv_array[11][2][10]=0.0486;//公积金 5-30年 4.86%%

//2007年	5月19日的新利率上限
lilv_array[12]=new Array;
lilv_array[12][1]=new Array;
lilv_array[12][2]=new Array;
lilv_array[12][1][5]=0.0693;//商贷 1～5年 6.93%
lilv_array[12][1][10]=0.0720;//商贷 5-30年 7.20%
lilv_array[12][2][5]=0.0441;//公积金 1～5年 4.41%%
lilv_array[12][2][10]=0.0486;//公积金 5-30年 4.86%%

//2007年	7月21日的新利率下限
lilv_array[13]=new Array;
lilv_array[13][1]=new Array;
lilv_array[13][2]=new Array;
lilv_array[13][1][5]=0.0612;//商贷 1～5年 6.12%
lilv_array[13][1][10]=0.06273;//商贷 5-30年 6.273%
lilv_array[13][2][5]=0.0450;//公积金 1～5年 4.50%%
lilv_array[13][2][10]=0.0495;//公积金 5-30年 4.95%%

//2007年	7月21日的新利率上限
lilv_array[14]=new Array;
lilv_array[14][1]=new Array;
lilv_array[14][2]=new Array;
lilv_array[14][1][5]=0.0720;//商贷 1～5年 7.20%
lilv_array[14][1][10]=0.0738;//商贷 5-30年 7.38%
lilv_array[14][2][5]=0.0450;//公积金 1～5年 4.50%%
lilv_array[14][2][10]=0.0495;//公积金 5-30年 4.95%%

//2007年	8月22日的新利率下限
lilv_array[15]=new Array;
lilv_array[15][1]=new Array;
lilv_array[15][2]=new Array;
lilv_array[15][1][5]=0.06273;//商贷 1～5年 6.273%
lilv_array[15][1][10]=0.06426;//商贷 5-30年 6.426%
lilv_array[15][2][5]=0.0459;//公积金 1～5年 4.59%
lilv_array[15][2][10]=0.0504;//公积金 5-30年 5.04%

//2007年	8月22日的新利率上限
lilv_array[16]=new Array;
lilv_array[16][1]=new Array;
lilv_array[16][2]=new Array;
lilv_array[16][1][5]=0.0738;//商贷 1～5年 7.38%
lilv_array[16][1][10]=0.0756;//商贷 5-30年 7.56%
lilv_array[16][2][5]=0.0459;//公积金 1～5年 4.59%
lilv_array[16][2][10]=0.0504;//公积金 5-30年 5.04%

//2007年	9月15日的新利率下限
lilv_array[17]=new Array;
lilv_array[17][1]=new Array;
lilv_array[17][2]=new Array;
lilv_array[17][1][5]=0.06503;//商贷 1～5年 6.503%
lilv_array[17][1][10]=0.06656;//商贷 5-30年 6.656%
lilv_array[17][2][5]=0.0477;//公积金 1～5年 4.77%
lilv_array[17][2][10]=0.0522;//公积金 5-30年 5.22%

//2007年	9月15日的新利率上限
lilv_array[18]=new Array;
lilv_array[18][1]=new Array;
lilv_array[18][2]=new Array;
lilv_array[18][1][5]=0.0765;//商贷 1～5年 7.65%
lilv_array[18][1][10]=0.0783;//商贷 5-30年 7.83%
lilv_array[18][2][5]=0.0477;//公积金 1～5年 4.77%
lilv_array[18][2][10]=0.0522;//公积金 5-30年 5.22%

//2007年	9月15日新利率(第二套房)
lilv_array[19]=new Array;
lilv_array[19][1]=new Array;
lilv_array[19][2]=new Array;
lilv_array[19][1][5]=0.08415;//商贷 1～5年 8.415%
lilv_array[19][1][10]=0.08613;//商贷 5-30年 8.613%
lilv_array[19][2][5]=0.0477;//公积金 1～5年 4.77%
lilv_array[19][2][10]=0.0522;//公积金 5-30年 5.22%


//2007年	12月21日的新利率下限
lilv_array[20]=new Array;
lilv_array[20][1]=new Array;
lilv_array[20][2]=new Array;
lilv_array[20][1][5]=0.06579;//商贷 1～5年 6.579%
lilv_array[20][1][10]=0.06656;//商贷 5-30年 6.656%
lilv_array[20][2][5]=0.0477;//公积金 1～5年 4.77%
lilv_array[20][2][10]=0.0522;//公积金 5-30年 5.22%

//2007年	12月21日的新利率上限
lilv_array[21]=new Array;
lilv_array[21][1]=new Array;
lilv_array[21][2]=new Array;
lilv_array[21][1][5]=0.0851;//商贷 1～5年 8.514%
lilv_array[21][1][10]=0.0861;//商贷 5-30年 8.613%
lilv_array[21][2][5]=0.0477;//公积金 1～5年 4.77%
lilv_array[21][2][10]=0.0522;//公积金 5-30年 5.22%

//2007年	12月21日新利率(第二套房)
lilv_array[22]=new Array;
lilv_array[22][1]=new Array;
lilv_array[22][2]=new Array;
lilv_array[22][1][5]=0.0851;//商贷 1～5年 8.514%
lilv_array[22][1][10]=0.0861;//商贷 5-30年 8.613%
lilv_array[22][2][5]=0.0477;//公积金 1～5年 4.77%
lilv_array[22][2][10]=0.0522;//公积金 5-30年 5.22%

//2008年	09月16日新利率下限
lilv_array[23]=new Array;
lilv_array[23][1]=new Array;
lilv_array[23][2]=new Array;
lilv_array[23][1][5]=0.06426;//商贷 1～5年 6.426%
lilv_array[23][1][10]=0.06579;//商贷 5-30年 6.579%
lilv_array[23][2][5]=0.0459;//公积金 1～5年 4.59%
lilv_array[23][2][10]=0.0513;//公积金 5-30年 5.13%

//2008年	09月16日新利率上限
lilv_array[24]=new Array;
lilv_array[24][1]=new Array;
lilv_array[24][2]=new Array;
lilv_array[24][1][5]=0.0832;//商贷 1～5年 8.32%
lilv_array[24][1][10]=0.0851;//商贷 5-30年 8.51%
lilv_array[24][2][5]=0.0459;//公积金 1～5年 4.59%
lilv_array[24][2][10]=0.0513;//公积金 5-30年 5.13%

//2008年	09月16日新利率(第二套房)
lilv_array[25]=new Array;
lilv_array[25][1]=new Array;
lilv_array[25][2]=new Array;
lilv_array[25][1][5]=0.0832;//商贷 1～5年 8.32%
lilv_array[25][1][10]=0.0851;//商贷 5-30年 8.51%
lilv_array[25][2][5]=0.0459;//公积金 1～5年 4.59%
lilv_array[25][2][10]=0.0513;//公积金 5-30年 5.13%

//2008年	10月09日新利率下限
lilv_array[26]=new Array;
lilv_array[26][1]=new Array;
lilv_array[26][2]=new Array;
lilv_array[26][1][5]=0.062;//商贷 1～5年 6.20%
lilv_array[26][1][10]=0.0635;//商贷 5-30年 6.35%
lilv_array[26][2][5]=0.0432;//公积金 1～5年 4.32%
lilv_array[26][2][10]=0.0486;//公积金 5-30年 4.86%

//2008年	10月09日新利率上限
lilv_array[27]=new Array;
lilv_array[27][1]=new Array;
lilv_array[27][2]=new Array;
lilv_array[27][1][5]=0.0802;//商贷 1～5年 8.02%
lilv_array[27][1][10]=0.0822;//商贷 5-30年 8.22%
lilv_array[27][2][5]=0.0432;//公积金 1～5年 4.32%
lilv_array[27][2][10]=0.0486;//公积金 5-30年 4.86%

//2008年	10月09日新利率(第二套房)
lilv_array[28]=new Array;
lilv_array[28][1]=new Array;
lilv_array[28][2]=new Array;
lilv_array[28][1][5]=0.0802;//商贷 1～5年 8.02%
lilv_array[28][1][10]=0.0822;//商贷 5-30年 8.22%
lilv_array[28][2][5]=0.0432;//公积金 1～5年 4.32%
lilv_array[28][2][10]=0.0486;//公积金 5-30年 4.86%

//2008年	10月27日新利率下限
lilv_array[29]=new Array;
lilv_array[29][1]=new Array;
lilv_array[29][2]=new Array;
lilv_array[29][1][5]=0.051;//商贷 1～5年 5.10%
lilv_array[29][1][10]=0.0523;//商贷 5-30年 5.23%
lilv_array[29][2][5]=0.0405;//公积金 1～5年 4.05%
lilv_array[29][2][10]=0.0459;//公积金 5-30年 4.59%

//2008年	10月27日新利率上限
lilv_array[30]=new Array;
lilv_array[30][1]=new Array;
lilv_array[30][2]=new Array;
lilv_array[30][1][5]=0.0802;//商贷 1～5年 8.02%
lilv_array[30][1][10]=0.0822;//商贷 5-30年 8.22%
lilv_array[30][2][5]=0.0405;//公积金 1～5年 4.05%
lilv_array[30][2][10]=0.0459;//公积金 5-30年 4.59%

//2008年	10月27日新利率基准
lilv_array[31]=new Array;
lilv_array[31][1]=new Array;
lilv_array[31][2]=new Array;
lilv_array[31][1][5]=0.0729;//商贷 1～5年 7.29%
lilv_array[31][1][10]=0.0747;//商贷 5-30年 7.47%
lilv_array[31][2][5]=0.0405;//公积金 1～5年 4.05%
lilv_array[31][2][10]=0.0459;//公积金 5-30年 4.59%

//2008年	10月27日新利率(第二套房)
lilv_array[32]=new Array;
lilv_array[32][1]=new Array;
lilv_array[32][2]=new Array;
lilv_array[32][1][5]=0.0802;//商贷 1～5年 8.02%
lilv_array[32][1][10]=0.0822;//商贷 5-30年 8.22%
lilv_array[32][2][5]=0.0405;//公积金 1～5年 4.05%
lilv_array[32][2][10]=0.0459;//公积金 5-30年 4.59%

//2008年	10月30日新利率下限
lilv_array[33]=new Array;
lilv_array[33][1]=new Array;
lilv_array[33][2]=new Array;
lilv_array[33][1][5]=0.0491;//商贷 1～5年 4.91%
lilv_array[33][1][10]=0.0504;//商贷 5-30年 5.04%
lilv_array[33][2][5]=0.0405;//公积金 1～5年 4.05%
lilv_array[33][2][10]=0.0459;//公积金 5-30年 4.59%

//2008年	10月30日新利率上限
lilv_array[34]=new Array;
lilv_array[34][1]=new Array;
lilv_array[34][2]=new Array;
lilv_array[34][1][5]=0.0772;//商贷 1～5年 7.72%
lilv_array[34][1][10]=0.0792;//商贷 5-30年 7.92%
lilv_array[34][2][5]=0.0405;//公积金 1～5年 4.05%
lilv_array[34][2][10]=0.0459;//公积金 5-30年 4.59%

//2008年	10月30日新利率基准
lilv_array[35]=new Array;
lilv_array[35][1]=new Array;
lilv_array[35][2]=new Array;
lilv_array[35][1][5]=0.0702;//商贷 1～5年 7.02%
lilv_array[35][1][10]=0.0720;//商贷 5-30年 7.20%
lilv_array[35][2][5]=0.0405;//公积金 1～5年 4.05%
lilv_array[35][2][10]=0.0459;//公积金 5-30年 4.59%

//2008年	10月30日新利率(第二套房)
lilv_array[36]=new Array;
lilv_array[36][1]=new Array;
lilv_array[36][2]=new Array;
lilv_array[36][1][5]=0.0772;//商贷 1～5年 7.72%
lilv_array[36][1][10]=0.0792;//商贷 5-30年 7.92%
lilv_array[36][2][5]=0.0405;//公积金 1～5年 4.05%
lilv_array[36][2][10]=0.0459;//公积金 5-30年 4.59%

//2008年	11月27日新利率下限
lilv_array[37]=new Array;
lilv_array[37][1]=new Array;
lilv_array[37][2]=new Array;
lilv_array[37][1][5]=0.0416;//商贷 1～5年 4.16%
lilv_array[37][1][10]=0.0428;//商贷 5-30年 4.28%
lilv_array[37][2][5]=0.0351;//公积金 1～5年 3.51%
lilv_array[37][2][10]=0.0405;//公积金 5-30年 4.05%

//2008年	11月27日新利率上限
lilv_array[38]=new Array;
lilv_array[38][1]=new Array;
lilv_array[38][2]=new Array;
lilv_array[38][1][5]=0.0653;//商贷 1～5年 6.53%
lilv_array[38][1][10]=0.0673;//商贷 5-30年 6.73%
lilv_array[38][2][5]=0.0351;//公积金 1～5年 3.51%
lilv_array[38][2][10]=0.0405;//公积金 5-30年 4.05%

//2008年	11月27日新利率基准
lilv_array[39]=new Array;
lilv_array[39][1]=new Array;
lilv_array[39][2]=new Array;
lilv_array[39][1][5]=0.0594;//商贷 1～5年 5.94%
lilv_array[39][1][10]=0.0612;//商贷 5-30年 6.12%
lilv_array[39][2][5]=0.0351;//公积金 1～5年 3.51%
lilv_array[39][2][10]=0.0405;//公积金 5-30年 4.05%

//2008年	11月27日新利率(第二套房)
lilv_array[40]=new Array;
lilv_array[40][1]=new Array;
lilv_array[40][2]=new Array;
lilv_array[40][1][5]=0.0653;//商贷 1～5年 6.53%
lilv_array[40][1][10]=0.0673;//商贷 5-30年 6.73%
lilv_array[40][2][5]=0.0351;//公积金 1～5年 3.51%
lilv_array[40][2][10]=0.0405;//公积金 5-30年 4.05%

/*//2008年	12月23日新利率下限(7折)
lilv_array[41]=new Array;
lilv_array[41][1]=new Array;
lilv_array[41][2]=new Array;
lilv_array[41][1][5]=0.0403;//商贷 1～5年 4.03%
lilv_array[41][1][10]=0.0416;//商贷 5-30年 4.16%
lilv_array[41][2][5]=0.0333;//公积金 1～5年 3.33%
lilv_array[41][2][10]=0.0387;//公积金 5-30年 3.87%

//2008年	12月23日新利率下限(85折)
lilv_array[42]=new Array;
lilv_array[42][1]=new Array;
lilv_array[42][2]=new Array;
lilv_array[42][1][5]=0.0490;//商贷 1～5年 4.90%
lilv_array[42][1][10]=0.0505;//商贷 5-30年 5.05%
lilv_array[42][2][5]=0.0333;//公积金 1～5年 3.33%
lilv_array[42][2][10]=0.0387;//公积金 5-30年 3.87%

//2008年	12月23日新利率上限(1.1倍)
lilv_array[43]=new Array;
lilv_array[43][1]=new Array;
lilv_array[43][2]=new Array;
lilv_array[43][1][5]=0.0634;//商贷 1～5年 6.34%
lilv_array[43][1][10]=0.0653;//商贷 5-30年 6.53%
lilv_array[43][2][5]=0.0333;//公积金 1～5年 3.33%
lilv_array[43][2][10]=0.0387;//公积金 5-30年 3.87%

//2008年	12月23日新利率基准
lilv_array[44]=new Array;
lilv_array[44][1]=new Array;
lilv_array[44][2]=new Array;
lilv_array[44][1][5]=0.0576;//商贷 1～5年 5.76%
lilv_array[44][1][10]=0.0594;//商贷 5-30年 5.94%
lilv_array[44][2][5]=0.0333;//公积金 1～5年 3.33%
lilv_array[44][2][10]=0.0387;//公积金 5-30年 3.87%

//2008年	12月23日新利率(第二套房)(1.1倍)
lilv_array[45]=new Array;
lilv_array[45][1]=new Array;
lilv_array[45][2]=new Array;
lilv_array[45][1][5]=0.0634;//商贷 1～5年 6.34%
lilv_array[45][1][10]=0.0653;//商贷 5-30年 6.53%
lilv_array[45][2][5]=0.0333;//公积金 1～5年 3.33%
lilv_array[45][2][10]=0.0387;//公积金 5-30年 3.87%*/

//2010年	10月20日新利率下限(7折)
lilv_array[41]=new Array;
lilv_array[41][1]=new Array;
lilv_array[41][2]=new Array;
lilv_array[41][1][1]=0.03892;//商贷 1年 5.56%
lilv_array[41][1][3]=0.0392;//商贷 1～3年 5.60%
lilv_array[41][1][5]=0.04172;//商贷 3～5年 5.96%
lilv_array[41][1][10]=0.04298;//商贷 5-30年 6.14%
lilv_array[41][2][1]=0.0350;//公积金 1年 3.50%
lilv_array[41][2][3]=0.0350;//公积金 1～3年 3.50%
lilv_array[41][2][5]=0.0350;//公积金 3～5年 3.50%
lilv_array[41][2][10]=0.0405;//公积金 5-30年 4.05%

//2010年	10月20日新利率下限(75折)
lilv_array[42]=new Array;
lilv_array[42][1]=new Array;
lilv_array[42][2]=new Array;
lilv_array[42][1][1]=0.0417;//商贷 1年 5.56%
lilv_array[42][1][3]=0.042;//商贷 1～3年 5.60%
lilv_array[42][1][5]=0.0447;//商贷 3～5年 5.96%
lilv_array[42][1][10]=0.04605;//商贷 5-30年 6.14%
lilv_array[42][2][1]=0.0350;//公积金 1年 3.50%
lilv_array[42][2][3]=0.0350;//公积金 1～3年 3.50%
lilv_array[42][2][5]=0.0350;//公积金 3～5年 3.50%
lilv_array[42][2][10]=0.0405;//公积金 5-30年 4.05%

//2010年	10月20日新利率下限(8折)
lilv_array[43]=new Array;
lilv_array[43][1]=new Array;
lilv_array[43][2]=new Array;
lilv_array[43][1][1]=0.04448;//商贷 1年 5.56%
lilv_array[43][1][3]=0.0448;//商贷 1～3年 5.60%
lilv_array[43][1][5]=0.04768;//商贷 3～5年 5.96%
lilv_array[43][1][10]=0.04912;//商贷 5-30年 6.14%
lilv_array[43][2][1]=0.0350;//公积金 1年 3.50%
lilv_array[43][2][3]=0.0350;//公积金 1～3年 3.50%
lilv_array[43][2][5]=0.0350;//公积金 3～5年 3.50%
lilv_array[43][2][10]=0.0405;//公积金 5-30年 4.05%

//2010年	10月20日新利率下限(85折)
lilv_array[44]=new Array;
lilv_array[44][1]=new Array;
lilv_array[44][2]=new Array;
lilv_array[44][1][1]=0.04726;//商贷 1年 5.56%
lilv_array[44][1][3]=0.0476;//商贷 1～3年 5.60%
lilv_array[44][1][5]=0.05066;//商贷 3～5年 5.96%
lilv_array[44][1][10]=0.05219;//商贷 5-30年 6.14%
lilv_array[44][2][1]=0.0350;//公积金 1年 3.50%
lilv_array[44][2][3]=0.0350;//公积金 1～3年 3.50%
lilv_array[44][2][5]=0.0350;//公积金 3～5年 3.50%
lilv_array[44][2][10]=0.0405;//公积金 5-30年 4.05%

//2010年	10月20日新利率下限(9折)
lilv_array[45]=new Array;
lilv_array[45][1]=new Array;
lilv_array[45][2]=new Array;
lilv_array[45][1][1]=0.05004;//商贷 1年 5.56%
lilv_array[45][1][3]=0.0504;//商贷 1～3年 5.60%
lilv_array[45][1][5]=0.05364;//商贷 3～5年 5.96%
lilv_array[45][1][10]=0.05526;//商贷 5-30年 6.14%
lilv_array[45][2][1]=0.0350;//公积金 1年 3.50%
lilv_array[45][2][3]=0.0350;//公积金 1～3年 3.50%
lilv_array[45][2][5]=0.0350;//公积金 3～5年 3.50%
lilv_array[45][2][10]=0.0405;//公积金 5-30年 4.05%

//2010年	10月20日新利率上限(1.1倍)
lilv_array[46]=new Array;
lilv_array[46][1]=new Array;
lilv_array[46][2]=new Array;
lilv_array[46][1][1]=0.06116;//商贷 1年 6.116%
lilv_array[46][1][3]=0.0616;//商贷 1～3年 5.60%
lilv_array[46][1][5]=0.06556;//商贷 3～5年 5.96%
lilv_array[46][1][10]=0.06754;//商贷 5-30年 6.14%
lilv_array[46][2][1]=0.0350;//公积金 1年 3.50%
lilv_array[46][2][3]=0.0350;//公积金 1～3年 3.50%
lilv_array[46][2][5]=0.0350;//公积金 3～5年 3.50%
lilv_array[46][2][10]=0.0405;//公积金 5-30年 4.05%

//2010年	10月20日新利率基准
lilv_array[47]=new Array;
lilv_array[47][1]=new Array;
lilv_array[47][2]=new Array;
lilv_array[47][1][1]=0.0556;//商贷 1年 5.56%
lilv_array[47][1][3]=0.0560;//商贷 1～3年 5.60%
lilv_array[47][1][5]=0.0596;//商贷 3～5年 5.96%
lilv_array[47][1][10]=0.0614;//商贷 5-30年 6.14%
lilv_array[47][2][1]=0.0350;//公积金 1年 3.50%
lilv_array[47][2][3]=0.0350;//公积金 1～3年 3.50%
lilv_array[47][2][5]=0.0350;//公积金 3～5年 3.50%
lilv_array[47][2][10]=0.0405;//公积金 5-30年 4.05%

//2010年	10月20日新利率(第二套房)(1.1倍)
lilv_array[48]=new Array;
lilv_array[48][1]=new Array;
lilv_array[48][2]=new Array;
lilv_array[48][1][5]=0.0634;//商贷 1～5年 6.34%
lilv_array[48][1][10]=0.0653;//商贷 5-30年 6.53%
lilv_array[48][2][5]=0.0385;//公积金 1～5年 3.85%
lilv_array[48][2][10]=0.04455;//公积金 5-30年 4.455%


//2010年12月26日新利率
//2010年12月26日新利率下限(7折)
lilv_array[49]=new Array;
lilv_array[49][1]=new Array;
lilv_array[49][2]=new Array;
lilv_array[49][1][1]=0.04067;//商贷 1年 5.81%
lilv_array[49][1][3]=0.04095;//商贷 1～3年 5.85%
lilv_array[49][1][5]=0.04354;//商贷 3～5年 6.22%
lilv_array[49][1][10]=0.0448;//商贷 5-30年 6.40%
lilv_array[49][2][1]=0.0375;//公积金 1年 3.75%
lilv_array[49][2][3]=0.0375;//公积金 1～3年 3.75%
lilv_array[49][2][5]=0.0375;//公积金 3～5年 3.75%
lilv_array[49][2][10]=0.043;//公积金 5-30年 4.3%

//2010年12月26日新利率下限(75折)
lilv_array[50]=new Array;
lilv_array[50][1]=new Array;
lilv_array[50][2]=new Array;
lilv_array[50][1][1]=0.043575;//商贷 1年 5.81%
lilv_array[50][1][3]=0.043875;//商贷 1～3年 5.85%
lilv_array[50][1][5]=0.04665;//商贷 3～5年 6.22%
lilv_array[50][1][10]=0.048;//商贷 5-30年 6.4%
lilv_array[50][2][1]=0.0375;//公积金 1年 3.75%
lilv_array[50][2][3]=0.0375;//公积金 1～3年 3.75%
lilv_array[50][2][5]=0.0375;//公积金 3～5年 3.75%
lilv_array[50][2][10]=0.043;//公积金 5-30年 4.3%

//2010年12月26日新利率下限(8折)
lilv_array[51]=new Array;
lilv_array[51][1]=new Array;
lilv_array[51][2]=new Array;
lilv_array[51][1][1]=0.04648;//商贷 1年 5.81%
lilv_array[51][1][3]=0.0468;//商贷 1～3年 5.85%
lilv_array[51][1][5]=0.04976;//商贷 3～5年 6.22%
lilv_array[51][1][10]=0.0512;//商贷 5-30年 6.4%
lilv_array[51][2][1]=0.0375;//公积金 1年 3.75%
lilv_array[51][2][3]=0.0375;//公积金 1～3年 3.75%
lilv_array[51][2][5]=0.0375;//公积金 3～5年 3.75%
lilv_array[51][2][10]=0.043;//公积金 5-30年 4.3%

//2010年12月26日新利率下限(85折)
lilv_array[52]=new Array;
lilv_array[52][1]=new Array;
lilv_array[52][2]=new Array;
lilv_array[52][1][1]=0.049385;//商贷 1年 5.81%
lilv_array[52][1][3]=0.049725;//商贷 1～3年 5.85%
lilv_array[52][1][5]=0.05287;//商贷 3～5年 6.22%
lilv_array[52][1][10]=0.0544;//商贷 5-30年 6.4%
lilv_array[52][2][1]=0.0375;//公积金 1年 3.75%
lilv_array[52][2][3]=0.0375;//公积金 1～3年 3.75%
lilv_array[52][2][5]=0.0375;//公积金 3～5年 3.75%
lilv_array[52][2][10]=0.043;//公积金 5-30年 4.3%

//2010年12月26日新利率下限(9折)
lilv_array[53]=new Array;
lilv_array[53][1]=new Array;
lilv_array[53][2]=new Array;
lilv_array[53][1][1]=0.05229;//商贷 1年 5.81%
lilv_array[53][1][3]=0.05265;//商贷 1～3年 5.85%
lilv_array[53][1][5]=0.05598;//商贷 3～5年 6.22%
lilv_array[53][1][10]=0.0576;//商贷 5-30年 6.4%
lilv_array[53][2][1]=0.0375;//公积金 1年 3.75%
lilv_array[53][2][3]=0.0375;//公积金 1～3年 3.75%
lilv_array[53][2][5]=0.0375;//公积金 3～5年 3.75%
lilv_array[53][2][10]=0.043;//公积金 5-30年 4.3%

//2010年12月26日新利率上限(1.1倍)
lilv_array[54]=new Array;
lilv_array[54][1]=new Array;
lilv_array[54][2]=new Array;
lilv_array[54][1][1]=0.06391;//商贷 1年 5.81%
lilv_array[54][1][3]=0.06435;//商贷 1～3年 5.85%
lilv_array[54][1][5]=0.06842;//商贷 3～5年 6.22%
lilv_array[54][1][10]=0.0704;//商贷 5-30年 6.4%
lilv_array[54][2][1]=0.0375;//公积金 1年 3.75%
lilv_array[54][2][3]=0.0375;//公积金 1～3年 3.75%
lilv_array[54][2][5]=0.0375;//公积金 3～5年 3.75%
lilv_array[54][2][10]=0.043;//公积金 5-30年 4.3%

//2010年12月26日新利率基准
lilv_array[55]=new Array;
lilv_array[55][1]=new Array;
lilv_array[55][2]=new Array;
lilv_array[55][1][1]=0.0581;//商贷 1年 5.81%
lilv_array[55][1][3]=0.0585;//商贷 1～3年 5.85%
lilv_array[55][1][5]=0.0622;//商贷 3～5年 6.22%
lilv_array[55][1][10]=0.064;//商贷 5-30年 6.4%
lilv_array[55][2][1]=0.0375;//公积金 1年 3.75%
lilv_array[55][2][3]=0.0375;//公积金 1～3年 3.75%
lilv_array[55][2][5]=0.0375;//公积金 3～5年 3.75%
lilv_array[55][2][10]=0.043;//公积金 5-30年 4.3%

//2010年12月26日新利率(第二套房)(1.1倍)
lilv_array[56]=new Array;
lilv_array[56][1]=new Array;
lilv_array[56][2]=new Array;
lilv_array[56][1][5]=0.06842;//商贷 1～5年6.22%
lilv_array[56][1][10]=0.0704;//商贷 5-30年6.4%
lilv_array[56][2][5]=0.04125;//公积金 1～5年 3.75%
lilv_array[56][2][10]=0.0473;//公积金 5-30年 4.3%

//2011年02月09日新利率基准
lilv_array[57]=new Array;
lilv_array[57][1]=new Array;
lilv_array[57][2]=new Array;
lilv_array[57][1][1]=0.0606;//商贷 1年 6.06%
lilv_array[57][1][3]=0.061;//商贷 1～3年 6.1%
lilv_array[57][1][5]=0.0647;//商贷 3～5年 6.47%
lilv_array[57][1][10]=0.0665;//商贷 5-30年 6.65%
lilv_array[57][2][1]=0.04;//公积金 1年 4%
lilv_array[57][2][3]=0.04;//公积金 1～3年 4%
lilv_array[57][2][5]=0.04;//公积金 3～5年 4%
lilv_array[57][2][10]=0.045;//公积金 5-30年 4.3%

//2011年02月09日新利率(第二套房)(1.1倍)
lilv_array[58]=new Array;
lilv_array[58][1]=new Array;
lilv_array[58][2]=new Array;
lilv_array[58][1][5]=0.07117;//商贷 1～5年7.117%
lilv_array[58][1][10]=0.07315;//商贷 5-30年7.315%
lilv_array[58][2][5]=0.044;//公积金 1～5年 4.4%
lilv_array[58][2][10]=0.0495;//公积金 5-30年 4.95%

//2011年02月09日新利率下限(9折)
lilv_array[59]=new Array;
lilv_array[59][1]=new Array;
lilv_array[59][2]=new Array;
lilv_array[59][1][1]=0.05454;//商贷 1年 5.454%
lilv_array[59][1][3]=0.0549;//商贷 1～3年 5.49%
lilv_array[59][1][5]=0.05823;//商贷 3～5年 5.823%
lilv_array[59][1][10]=0.05985;//商贷 5-30年 5.985%
lilv_array[59][2][1]=0.04;//公积金 1年 4%
lilv_array[59][2][3]=0.04;//公积金 1～3年 4%
lilv_array[59][2][5]=0.04;//公积金 3～5年 4%
lilv_array[59][2][10]=0.0495;//公积金 5-30年 4.95%

//2011年02月09日新利率下限(8.5折)
lilv_array[60]=new Array;
lilv_array[60][1]=new Array;
lilv_array[60][2]=new Array;
lilv_array[60][1][1]=0.05151;//商贷 1年 5.151%
lilv_array[60][1][3]=0.05185;//商贷 1～3年 5.185%
lilv_array[60][1][5]=0.054995;//商贷 3～5年 5.4995%
lilv_array[60][1][10]=0.056525;//商贷 5-30年 5.6525%
lilv_array[60][2][1]=0.04;//公积金 1年 4%
lilv_array[60][2][3]=0.04;//公积金 1～3年 4%
lilv_array[60][2][5]=0.04;//公积金 3～5年 4%
lilv_array[60][2][10]=0.0495;//公积金 5-30年 4.95%

//2011年04月06日新利率基准
lilv_array[61]=new Array;
lilv_array[61][1]=new Array;
lilv_array[61][2]=new Array;
lilv_array[61][1][1]=0.0631;//商贷 1年 6.31%
lilv_array[61][1][3]=0.064;//商贷 1～3年 6.4%
lilv_array[61][1][5]=0.0665;//商贷 3～5年 6.65%
lilv_array[61][1][10]=0.068;//商贷 5-30年 6.8%
lilv_array[61][2][1]=0.042;//公积金 1年 4.2%
lilv_array[61][2][3]=0.042;//公积金 1～3年 4.2%
lilv_array[61][2][5]=0.042;//公积金 3～5年 4.2%
lilv_array[61][2][10]=0.047;//公积金 5-30年 4.3%

//2011年04月06日新利率(第二套房)(1.1倍)
lilv_array[62]=new Array;
lilv_array[62][1]=new Array;
lilv_array[62][2]=new Array;
lilv_array[62][1][5]=0.07315;//商贷 1～5年7.315%
lilv_array[62][1][10]=0.0748;//商贷 5-30年7.48%
lilv_array[62][2][5]=0.0462;//公积金 1～5年 4.62%
lilv_array[62][2][10]=0.0517;//公积金 5-30年 5.17%

//2011年04月06日新利率下限(9折)
lilv_array[63]=new Array;
lilv_array[63][1]=new Array;
lilv_array[63][2]=new Array;
lilv_array[63][1][1]=0.05679;//商贷 1年 5.679%
lilv_array[63][1][3]=0.0576;//商贷 1～3年 5.76%
lilv_array[63][1][5]=0.05985;//商贷 3～5年 5.985%
lilv_array[63][1][10]=0.0612;//商贷 5-30年 6.12%
lilv_array[63][2][1]=0.378;//公积金 1年 4%
lilv_array[63][2][3]=0.378;//公积金 1～3年 4%
lilv_array[63][2][5]=0.378;//公积金 3～5年 4%
lilv_array[63][2][10]=0.0423;//公积金 5-30年 4.23%

//2011年04月06日新利率下限(8.5折)
lilv_array[64]=new Array;
lilv_array[64][1]=new Array;
lilv_array[64][2]=new Array;
lilv_array[64][1][1]=0.053635;//商贷 1年 5.3635%
lilv_array[64][1][3]=0.0544;//商贷 1～3年 5.44%
lilv_array[64][1][5]=0.056525;//商贷 3～5年 5.6525%
lilv_array[64][1][10]=0.0578;//商贷 5-30年 5.78%
lilv_array[64][2][1]=0.0357;//公积金 1年 357%
lilv_array[64][2][3]=0.0357;//公积金 1～3年 357%
lilv_array[64][2][5]=0.0357;//公积金 3～5年 357%
lilv_array[64][2][10]=0.03995;//公积金 5-30年 3.995%

//2011年04月06日新利率下限(1.2倍)
lilv_array[65]=new Array;
lilv_array[65][1]=new Array;
lilv_array[65][2]=new Array;
lilv_array[65][1][5]=0.0798;//商贷 1～5年7.315%
lilv_array[65][1][10]=0.0816;//商贷 5-30年7.48%
lilv_array[65][2][5]=0.0504;//公积金 1～5年 4.62%
lilv_array[65][2][10]=0.0564;//公积金 5-30年 5.17%

//2011年07月01日新利率基准
lilv_array[66]=new Array;
lilv_array[66][1]=new Array;
lilv_array[66][2]=new Array;
lilv_array[66][1][1]=0.0656;//商贷 1年
lilv_array[66][1][3]=0.0665;//商贷 1～3年
lilv_array[66][1][5]=0.069;//商贷 3～5年
lilv_array[66][1][10]=0.0705;//商贷 5-30年
lilv_array[66][2][1]=0.0445;//公积金 1年
lilv_array[66][2][3]=0.0445;//公积金 1～3年
lilv_array[66][2][5]=0.0445;//公积金 3～5年
lilv_array[66][2][10]=0.049;//公积金 5-30年

//2011年07月01日新利率(第二套房)(1.1倍)
lilv_array[67]=new Array;
lilv_array[67][1]=new Array;
lilv_array[67][2]=new Array;
lilv_array[67][1][5]=FloatMul(lilv_array[66][1][5],1.1);//商贷 1～5年
lilv_array[67][1][10]=FloatMul(lilv_array[66][1][10],1.1);//商贷 5-30年
lilv_array[67][2][5]=FloatMul(lilv_array[66][2][5],1.1);//公积金 1～5年
lilv_array[67][2][10]=FloatMul(lilv_array[66][2][10],1.1);//公积金 5-30年

//2011年07月01日新利率下限(9折)
lilv_array[68]=new Array;
lilv_array[68][1]=new Array;
lilv_array[68][2]=new Array;
lilv_array[68][1][1]=FloatMul(lilv_array[66][1][1],0.9);//商贷 1年
lilv_array[68][1][3]=FloatMul(lilv_array[66][1][3],0.9);//商贷 1～3年
lilv_array[68][1][5]=FloatMul(lilv_array[66][1][5],0.9);//商贷 3～5年
lilv_array[68][1][10]=FloatMul(lilv_array[66][1][10],0.9);//商贷 5-30年
lilv_array[68][2][1]=FloatMul(lilv_array[66][2][1],0.9);//公积金 1年
lilv_array[68][2][3]=FloatMul(lilv_array[66][2][3],0.9);//公积金 1～3年
lilv_array[68][2][5]=FloatMul(lilv_array[66][2][5],0.9);//公积金 3～5年
lilv_array[68][2][10]=FloatMul(lilv_array[66][2][10],0.9);//公积金 5-30年

//2011年07月01日新利率下限(8.5折)
lilv_array[69]=new Array;
lilv_array[69][1]=new Array;
lilv_array[69][2]=new Array;
lilv_array[69][1][1]=FloatMul(lilv_array[66][1][1],0.85);//商贷 1年
lilv_array[69][1][3]=FloatMul(lilv_array[66][1][3],0.85);//商贷 1～3年
lilv_array[69][1][5]=FloatMul(lilv_array[66][1][5],0.85);//商贷 3～5年
lilv_array[69][1][10]=FloatMul(lilv_array[66][1][10],0.85);//商贷 5-30年
lilv_array[69][2][1]=FloatMul(lilv_array[66][2][1],0.85);//公积金 1年
lilv_array[69][2][3]=FloatMul(lilv_array[66][2][3],0.85);//公积金 1～3年
lilv_array[69][2][5]=FloatMul(lilv_array[66][2][5],0.85);//公积金 3～5年
lilv_array[69][2][10]=FloatMul(lilv_array[66][2][10],0.85);//公积金 5-30年

//2011年07月01日新利率下限(1.2倍)
lilv_array[70]=new Array;
lilv_array[70][1]=new Array;
lilv_array[70][2]=new Array;
lilv_array[70][1][5]=FloatMul(lilv_array[66][1][5],1.2);//商贷 1～5年
lilv_array[70][1][10]=FloatMul(lilv_array[66][1][10],1.2);//商贷 5-30年
lilv_array[70][2][5]=FloatMul(lilv_array[66][2][5],1.2);//公积金 1～5年
lilv_array[70][2][10]=FloatMul(lilv_array[66][2][10],1.2);//公积金 5-30年


function exc_zuhe(fmobj,v){
	//var fmobj=document.calc1;
	if (fmobj.name=="calc1"){
		if (v==3){
			document.getElementById('calc1_zuhe').style.display='block';
			fmobj.jisuan_radio[1].checked = true;
			exc_js(fmobj,2);
		}else{document.getElementById('calc1_zuhe').style.display='none';}
	}else{
		if (v==3){
			document.getElementById('calc2_zuhe').style.display='block';
			fmobj.jisuan_radio[1].checked = true;
			exc_js(fmobj,2);
		}else{document.getElementById('calc2_zuhe').style.display='none';}
	}
}
function exc_js(fmobj,v){
	//var fmobj=document.calc1;
	if (fmobj.name=="calc1"){
		if (v==1){
			document.getElementById('calc1_js_div1').style.display='block';
			document.getElementById('calc1_js_div2').style.display='none';
			document.getElementById('calc1_zuhe').style.display='none';
			fmobj.type.value=1;
		}else{
			document.getElementById('calc1_js_div1').style.display='none';
			document.getElementById('calc1_js_div2').style.display='block';
		}
	}else{
		if (v==1){
			document.getElementById('calc2_js_div1').style.display='block';
			document.getElementById('calc2_js_div2').style.display='none';
			document.getElementById('calc2_zuhe').style.display='none';
			fmobj.type.value=1;
		}else{
			document.getElementById('calc2_js_div1').style.display='none';
			document.getElementById('calc2_js_div2').style.display='block';
		}
	}
}
function formReset(fmobj){
	//var fmobj=document.calc1;
	if (fmobj.name=="calc1"){
		document.getElementById('calc1_js_div1').style.display='block';
		document.getElementById('calc1_js_div2').style.display='none';
		document.getElementById('calc1_zuhe').style.display='none';
		//document.all.calc1_benjin.style.display='none';
	}else{
		document.getElementById('calc2_js_div1').style.display='block';
		document.getElementById('calc2_js_div2').style.display='none';
		document.getElementById('calc2_zuhe').style.display='none';
		//document.all.calc2_benxi.style.display='none';
	}
}

//显示右边的比较div
function showRightDiv(fmobj){
	if (ext_total(fmobj)==false){return;}
	//alert(document.getElementById('calc1').month_money2.value);
	var a=window.open('','calc_win','status=yes,scrollbars=yes,resizable=yes,width=550,height=500,left=0,top=0')//790*520
	if (fmobj.name=="calc1"){
		document.getElementById('calc1').target = "calc_win";
		document.getElementById('calc1').submit();
	}else{
		document.getElementById('calc2').target = "calc_win";
		document.getElementById('calc2').submit();
	}

}


//验证是否为数字
function reg_Num(str){
	if (str.length==0){return false;}
	var Letters = "1234567890.";

	for (i=0;i<str.length;i++){
		var CheckChar = str.charAt(i);
		if (Letters.indexOf(CheckChar) == -1){return false;}
	}
	return true;
}

//得到利率
function getlilv(lilv_class,type,years){
	var lilv_class = parseInt(lilv_class);
	
    if (years==1){
		 return lilv_array[lilv_class][type][1];
	}else if (years<=3){
		 return lilv_array[lilv_class][type][3];
	}else if (years<=5){
		 return lilv_array[lilv_class][type][5];
	}else{
		return lilv_array[lilv_class][type][10];
	}
}

//本金还款的月还款额(参数: 年利率 / 贷款总额 / 贷款总月份 / 贷款当前月0～length-1)
function getMonthMoney2(lilv,total,month,cur_month){
	var lilv_month = lilv / 12;//月利率
	//return total * lilv_month * Math.pow(1 + lilv_month, month) / ( Math.pow(1 + lilv_month, month) -1 );
	var benjin_money = total/month;
	return (total - benjin_money * cur_month) * lilv_month + benjin_money;

}


//本息还款的月还款额(参数: 年利率/贷款总额/贷款总月份)
function getMonthMoney1(lilv,total,month){
	var lilv_month = lilv / 12;//月利率
	return total * lilv_month * Math.pow(1 + lilv_month, month) / ( Math.pow(1 + lilv_month, month) -1 );
}

function ext_total(fmobj){
	//var fmobj=document.calc1;
/*	//先清空月还款数下拉框
	while ((k=fmobj.month_money2.length-1)>=0){
		fmobj.month_money2.options.remove(k);
	}*/
	var years = fmobj.years.value;
	var month = fmobj.years.value * 12;

	fmobj.month1.value = month;
	fmobj.month2.value = month;
	if (fmobj.type.value == 3 ){
		//--  组合型贷款(组合型贷款的计算，只和商业贷款额、和公积金贷款额有关，和按贷款总额计算无关)
		if (!reg_Num(fmobj.total_sy.value)){alert("混合型贷款请填写商贷比例");fmobj.total_sy.focus();return false;}
		if (!reg_Num(fmobj.total_gjj.value)){alert("混合型贷款请填写公积金比例");fmobj.total_gjj.focus();return false;}
		if (fmobj.total_sy.value==null){fmobj.total_sy.value=0;}
		if (fmobj.total_gjj.value==null){fmobj.total_gjj.value=0;}
		var total_sy = fmobj.total_sy.value;
		var total_gjj = fmobj.total_gjj.value;
		fmobj.fangkuan_total1.value = "略";//房款总额
		fmobj.fangkuan_total2.value = "略";//房款总额
		fmobj.money_first1.value = 0;//首期付款
		fmobj.money_first2.value = 0;//首期付款

		//贷款总额
		var total_sy = fmobj.total_sy.value*10000;
		var total_gjj = fmobj.total_gjj.value*10000;
		var daikuan_total = total_sy + total_gjj;
		fmobj.daikuan_total1.value = daikuan_total;
		fmobj.daikuan_total2.value = daikuan_total;

		//月还款
		var lilv_sd = getlilv(fmobj.lilv.value,1, years);//得到商贷利率
		var lilv_gjj = getlilv(fmobj.lilv.value,2, years);//得到公积金利率
		fmobj.gjjdkll.value = lilv_gjj;
		fmobj.sydkll.value = lilv_sd;

		//1.本金还款
		//月还款
		var all_total2 = 0;
		var month_money2 = "";
		for(j=0;j<month;j++) {
			//调用函数计算: 本金月还款额
			huankuan = getMonthMoney2(lilv_sd,total_sy,month,j) + getMonthMoney2(lilv_gjj,total_gjj,month,j);
			all_total2 += huankuan;
			huankuan = Math.round(huankuan*100)/100;
			//fmobj.month_money2.options[j] = new Option( (j+1) +"月," + huankuan + "(元)", huankuan);
			month_money2 += (j+1) +"月," + huankuan + "(元)\n";
		}
		fmobj.month_money2.value = month_money2;
		//还款总额
		fmobj.all_total2.value = Math.round(all_total2*100)/100;
		//支付利息款
		fmobj.accrual2.value = Math.round( (all_total2 - daikuan_total) *100)/100;

		//2.本息还款
		//月均还款
		var month_money1 = getMonthMoney1(lilv_sd,total_sy,month) + getMonthMoney1(lilv_gjj,total_gjj,month);//调用函数计算
		fmobj.month_money1.value = Math.round(month_money1*100)/100;
		//还款总额
		var all_total1 = month_money1 * month;
		fmobj.all_total1.value = Math.round(all_total1*100)/100;
		//支付利息款
		fmobj.accrual1.value = Math.round( (all_total1 - daikuan_total) *100)/100;
	}else{
		//--  商业贷款、公积金贷款
		var lilv = getlilv(fmobj.lilv.value,fmobj.type.value, fmobj.years.value);//得到利率
		if (fmobj.type.value == 1 ){
			fmobj.sydkll.value = FloatMul(lilv,100);
			fmobj.gjjdkll.value = '';
		}else{
			fmobj.gjjdkll.value = FloatMul(lilv,100);
			fmobj.sydkll.value = '';
		}
		if (fmobj.jisuan_radio[0].checked == true){
			//------------ 根据单价面积计算
			if (!reg_Num(fmobj.price.value)){alert("请填写单价");fmobj.price.focus();return false;}
			if (!reg_Num(fmobj.sqm.value)){alert("请填写面积");fmobj.sqm.focus();return false;}

			//房款总额
			var fangkuan_total = fmobj.price.value * fmobj.sqm.value;
			fmobj.fangkuan_total1.value = fangkuan_total;
			fmobj.fangkuan_total2.value = fangkuan_total;
			//贷款总额
			var daikuan_total = (fmobj.price.value * fmobj.sqm.value) * (fmobj.anjie.value/10);
			fmobj.daikuan_total1.value = daikuan_total;
			fmobj.daikuan_total2.value = daikuan_total;
			//首期付款
			var money_first = fangkuan_total - daikuan_total;
			fmobj.money_first1.value = money_first
			fmobj.money_first2.value = money_first;
		}else{
			//------------ 根据贷款总额计算
			if (fmobj.daikuan_total000.value.length==0){alert("请填写贷款总额");fmobj.daikuan_total000.focus();return false;}

			//房款总额
			fmobj.fangkuan_total1.value = "略";
			fmobj.fangkuan_total2.value = "略";
			//贷款总额
			var daikuan_total = fmobj.daikuan_total000.value*10000;
			fmobj.daikuan_total1.value = daikuan_total;
			fmobj.daikuan_total2.value = daikuan_total;
			//首期付款
			fmobj.money_first1.value = 0;
			fmobj.money_first2.value = 0;
		}
		//1.本金还款
		//月还款
		var all_total2 = 0;
		var month_money2 = "";
		for(j=0;j<month;j++) {
			//调用函数计算: 本金月还款额
			huankuan = getMonthMoney2(lilv,daikuan_total,month,j);
			all_total2 += huankuan;
			huankuan = Math.round(huankuan*100)/100;
			//fmobj.month_money2.options[j] = new Option( (j+1) +"月," + huankuan + "(元)", huankuan);
			month_money2 += (j+1) +"月," + huankuan + "(元)\n";
		}
		fmobj.month_money2.value = month_money2;
		//还款总额
		fmobj.all_total2.value = Math.round(all_total2*100)/100;
		//支付利息款
		fmobj.accrual2.value = Math.round( (all_total2 - daikuan_total) *100)/100;

		//2.本息还款
		//月均还款
		var month_money1 = getMonthMoney1(lilv,daikuan_total,month);//调用函数计算
		fmobj.month_money1.value = Math.round(month_money1*100)/100;
		//还款总额
		var all_total1 = month_money1 * month;
		fmobj.all_total1.value = Math.round(all_total1*100)/100;
		//支付利息款
		fmobj.accrual1.value = Math.round( (all_total1 - daikuan_total) *100)/100;
	}
}

// //提前还款计算
// function play(){
//   if (document.tqhdjsq.dkzws.value==''){
//        alert('请填入贷款总额');
//        return false;
//   }else dkzys=parseFloat(document.tqhdjsq.dkzws.value)*10000;

//   if(document.tqhdjsq.tqhkfs[1].checked && document.tqhdjsq.tqhkws.value==''){
//     alert('请填入部分提前还款额度');
//     return false;
//    }
//   s_yhkqs=parseInt(document.tqhdjsq.yhkqs.value);

//   //月利率

// 	if(document.tqhdjsq.dklx[0].checked){
// 		if (s_yhkqs>60){
// 			dklv=getlilv(document.tqhdjsq.dklv_class.value,2,10)/12; //公积金贷款利率5年以上4.23%
// 		}else{
// 			dklv=getlilv(document.tqhdjsq.dklv_class.value,2,3)/12;  //公积金贷款利率5年(含)以下3.78%
// 		}
// 	}
// 	if(document.tqhdjsq.dklx[1].checked){
// 		if (s_yhkqs>60){
// 			dklv=getlilv(document.tqhdjsq.dklv_class.value,1,10)/12; //商业性贷款利率5年以上5.31%
// 		}else{
// 			dklv=getlilv(document.tqhdjsq.dklv_class.value,1,3)/12; //商业性贷款利率5年(含)以下4.95%
// 		}
// 	}

//   //已还贷款期数
//   yhdkqs=(parseInt(document.tqhdjsq.tqhksjn.value)*12+parseInt(document.tqhdjsq.tqhksjy.value))-(parseInt(document.tqhdjsq.yhksjn.value)*12 + parseInt(document.tqhdjsq.yhksjy.value));

//   if(yhdkqs<0 || yhdkqs>s_yhkqs){
//     alert('预计提前还款时间与第一次还款时间有矛盾，请查实');
//     return false;
//    }

//   yhk=dkzys*(dklv*Math.pow((1+dklv),s_yhkqs))/(Math.pow((1+dklv),s_yhkqs)-1);
//   yhkjssj=Math.floor((parseInt(document.tqhdjsq.yhksjn.value)*12+parseInt(document.tqhdjsq.yhksjy.value)+s_yhkqs-2)/12)+'年'+((parseInt(document.tqhdjsq.yhksjn.value)*12+parseInt(document.tqhdjsq.yhksjy.value)+s_yhkqs-2)%12+1)+'月';
//   yhdkys=yhk*yhdkqs;

//   yhlxs=0;
//   yhbjs=0;
//   for(i=1;i<=yhdkqs;i++){
//      yhlxs=yhlxs+(dkzys-yhbjs)*dklv;
//      yhbjs=yhbjs+yhk-(dkzys-yhbjs)*dklv;
//    }

//   remark='';
//   if(document.tqhdjsq.tqhkfs[1].checked){
//     tqhkys=parseInt(document.tqhdjsq.tqhkws.value)*10000;
//      if(tqhkys+yhk>=(dkzys-yhbjs)*(1+dklv)){
//          remark='您的提前还款额已足够还清所欠贷款！';
//      }else{
// 	        yhbjs=yhbjs+yhk;
//             byhk=yhk+tqhkys;
// 			if(document.tqhdjsq.clfs[0].checked){
// 			  yhbjs_temp=yhbjs+tqhkys;
//               for(xdkqs=0;yhbjs_temp<=dkzys;xdkqs++) yhbjs_temp=yhbjs_temp+yhk-(dkzys-yhbjs_temp)*dklv;
// 			  xdkqs=xdkqs-1;
//               xyhk=(dkzys-yhbjs-tqhkys)*(dklv*Math.pow((1+dklv),xdkqs))/(Math.pow((1+dklv),xdkqs)-1);
//               jslx=yhk*s_yhkqs-yhdkys-byhk-xyhk*xdkqs;
// 			  xdkjssj=Math.floor((parseInt(document.tqhdjsq.tqhksjn.value)*12+parseInt(document.tqhdjsq.tqhksjy.value)+xdkqs-2)/12)+'年'+((parseInt(document.tqhdjsq.tqhksjn.value)*12+parseInt(document.tqhdjsq.tqhksjy.value)+xdkqs-2)%12+1)+'月'; 
//              }else{
// 		       xyhk=(dkzys-yhbjs-tqhkys)*(dklv*Math.pow((1+dklv),(s_yhkqs-yhdkqs)))/(Math.pow((1+dklv),(s_yhkqs-yhdkqs))-1);
//                jslx=yhk*s_yhkqs-yhdkys-byhk-xyhk*(s_yhkqs-yhdkqs);
// 			   xdkjssj=yhkjssj;
// 			  }
//        }
//    }

//   if(document.tqhdjsq.tqhkfs[0].checked || remark!=''){
//     byhk=(dkzys-yhbjs)*(1+dklv);
//     xyhk=0;
//     jslx=yhk*s_yhkqs-yhdkys-byhk;
//     xdkjssj=document.tqhdjsq.tqhksjn.value+'年'+document.tqhdjsq.tqhksjy.value+'月';
// 	}

//   document.tqhdjsq.ykhke.value=Math.round(yhk*100)/100;
//   document.tqhdjsq.yhkze.value=Math.round(yhdkys*100)/100;
//   document.tqhdjsq.yhlxe.value=Math.round(yhlxs*100)/100;
//   document.tqhdjsq.gyyihke.value=Math.round(byhk*100)/100;
//   document.tqhdjsq.xyqyhke.value=Math.round(xyhk*100)/100;
//   document.tqhdjsq.jslxzc.value=Math.round(jslx*100)/100;
//   document.tqhdjsq.yzhhkq.value=yhkjssj;
//   document.tqhdjsq.xdzhhkq.value=xdkjssj;
//   document.tqhdjsq.jsjgts.value=remark;
// }

// function ext_total2(fmobj){
// 	var total = fmobj.total.value;
// 	var result = 0;
	
// 	if(total <= 0){
// 		fmobj.result.value = '';
// 		return 0;
// 	}

// 	if(total <= 100) {
// 		result = total*0.0042;
// 	}else if(total >100 && total<=500){
// 		result = 100*0.0042+(total-100)*0.003;
// 	}else if(total >500 && total<=2000){
// 		result = 100*0.0042+400*0.003+(total-500)*0.0012;
// 	}else if(total >2000 && total<=5000){
// 		result = 100*0.0042+400*0.003+1500*0.0012+(5000-total)*0.0006;
// 	}else if(total >5000){
// 		result = 100*0.0042+400*0.003+1500*0.0012+3000*0.0006+(total-5000)*0.00012;
// 	}
	
// 	result = result*10000;
// 	fmobj.result.value = result;
// }

// function ext_total3(fmobj){
// 	var total = fmobj.total2.value;
// 	var result = 0;

// 	if(total <= 0){
// 		fmobj.result2.value = '';
// 		return 0;
// 	}

// 	if(total <= 50) {
// 		result = total*0.0125;
// 	}else if(total >50 && total<=100){
// 		result = 50*0.0125+(total-50)*0.00875;
// 	}else if(total >100 && total<=200){
// 		result =  50*0.0125+50*0.00875+(total-100)*0.005;
// 	}else if(total >200){
// 		result =  50*0.0125+50*0.00875+100*0.005+(total-200)*0.00375;
// 	}
	
// 	result = result*10000;
// 	fmobj.result2.value = result;
// }

// function getevent(){     //同时兼容ie和ff的写法
//     if(window.event)return window.event;        
//     func = getevent.caller;            
//     while(func!=null){    
//         var arg0=func.arguments[0];
//         if(arg0){
//             if((arg0.constructor==Event || arg0.constructor==MouseEvent) || (typeof(arg0)=="object" && arg0.preventdefault && arg0.stoppropagation)){
//                 return arg0;
//             }
//         }
//         func=func.caller;
//      }
//      return null;
// }
// //dwk 显示明细 2010年9月30日
// function showdetail(id){
// 	document.getElementById(id).style.display = 'block';
// }
// //dwk 隐藏明细 2010年9月30日
// function closedetail(id){
// 	document.getElementById(id).style.display = 'none';
// }
// //买房 印花税
// var yhsArr = [];
// yhsArr['flat'] = [];                      //住宅
// yhsArr['flat']['first'] = [];             //首套
// yhsArr['flat']['first']['small'] = 0;     //90平米以下的
// yhsArr['flat']['first']['middle'] = 0;    //90-140平米的
// yhsArr['flat']['first']['big'] = 0;       //140平米以上的
// yhsArr['flat']['sec'] = [];               //第二套
// yhsArr['flat']['sec']['small'] = 0;
// yhsArr['flat']['sec']['middle'] = 0;
// yhsArr['flat']['sec']['big'] = 0;
// yhsArr['other'] = 0.05;                   //非住宅
		
// //买房 产权交易手续费
// var cqjysxfArr = [];
// cqjysxfArr['flat'] = [];
// cqjysxfArr['flat']['first'] = [];
// cqjysxfArr['flat']['first']['small'] = 0;
// cqjysxfArr['flat']['first']['middle'] = 0;
// cqjysxfArr['flat']['first']['big'] = 3;
// cqjysxfArr['flat']['sec'] = [];
// cqjysxfArr['flat']['sec']['small'] = 0;
// cqjysxfArr['flat']['sec']['middle'] = 0;
// cqjysxfArr['flat']['sec']['big'] = 3;
// cqjysxfArr['other'] = 6;

// //买房 产权注册登记工本费
// var cqzcdjgbfArr = [];
// cqzcdjgbfArr['flat'] = 5;
// cqzcdjgbfArr['other'] = 555;

// //买房 共有权证
// var gyqzArr = [];
// gyqzArr['flat'] = 5;
// gyqzArr['other'] = 555;

// //买房 他项权证
// var txqzArr = [];
// txqzArr['flat'] = 85;
// txqzArr['other'] = 555;

// //买房 土地交易手续费
// var tdjysxfArr = [];
// tdjysxfArr['flat'] = [];
// tdjysxfArr['flat']['small'] = 0;
// tdjysxfArr['flat']['middle'] = 0;
// tdjysxfArr['flat']['big'] = 50;
// tdjysxfArr['other'] = 50;

// //买房 土地证工本费
// var tdzgbfArr = [];
// tdzgbfArr['flat'] = 5;
// tdzgbfArr['other'] = 18;

// var qs,yhs,cqjysxf,cqzcdjgbf,gyqz,txqz,tdjysxf,tdzgbf;
// //dwk 买房点击填写 2010年9月30日
// function buyselectok(id){
// 	var e = getevent();
	
// 	var o = e.srcElement || e.target;
// 	qs = o.getAttribute('val');
// 	if(qs != undefined){
// 		document.getElementById(id).value = qs+'%';
// 		var type = o.getAttribute('type');
// 		var space = o.getAttribute('space');
// 		var num = o.getAttribute('num');
// 		if(type == 'other'){
// 			yhs = yhsArr[type];
// 			cqjysxf = cqjysxfArr[type];
// 			tdjysxf = tdjysxfArr[type];
// 		}else{
// 			yhs = yhsArr[type][num][space];
// 			cqjysxf = cqjysxfArr[type][num][space];
// 			tdjysxf = tdjysxfArr[type][space];
// 		}
		
// 		cqzcdjgbf = cqzcdjgbfArr[type];
// 		gyqz = gyqzArr[type];
// 		txqz = txqzArr[type];
// 		tdzgbf = tdzgbfArr[type];
// 		closedetail(id + 'list');
// 	}
// }
// //dwk 税费计算 买房 2010年9月30日
// function buy(){
// 	var space = document.getElementById('qsspace').value;
// 	if(!reg_Num(space)){
// 		alert('请填写面积！');
// 		document.getElementById('qsspace').focus();
// 		return false;
// 	}
// 	var price = document.getElementById('qsprice').value;
// 	if(!reg_Num(price)){
// 		alert('请填写总价！');
// 		document.getElementById('qsprice').focus();
// 		return false;
// 	}
	
// 	if(qs==undefined || !reg_Num(qs)){
// 		alert('请选择契税！');
// 		showdetail('qsdetaillist');
// 		return false;
// 	}

	var allqs = Math.round((qs/100)*(price*10000));
	document.getElementById('qs').innerHTML = allqs + '元';
	var allyhs = Math.round((yhs/100)*(price*10000));
	document.getElementById('yhs').innerHTML = allyhs + '元';
	var allcqjysxf = Math.round((cqjysxf/100)*space);
	document.getElementById('cqjysxf').innerHTML = allcqjysxf + '元';
	document.getElementById('cqzcdjgbf').innerHTML = cqzcdjgbf + '元';
	document.getElementById('gyqz').innerHTML = gyqz + '元';
	document.getElementById('txqz').innerHTML = txqz + '元';
	document.getElementById('tdjysxf').innerHTML = tdjysxf + '元';
	document.getElementById('tdzgbf').innerHTML = tdzgbf + '元';
	var zj = allqs+allyhs+cqjysxf+cqzcdjgbf+gyqz+txqz+tdjysxf+tdzgbf;
	document.getElementById('zj').innerHTML = zj;
}

// /*//卖房 营业税及附加
// var yysjfjArr = [];
// yysjfjArr['flat'] = [];                  //住宅
// yysjfjArr['flat']['no'] = [];            //未满5年
// yysjfjArr['flat']['no']['normal'] = 5.6; //140以下 差额
// yysjfjArr['flat']['no']['big'] = 5.6;    //140以上 全额
// yysjfjArr['flat']['yes']['normal'] = 0;  //已满5年 140以下 免征
// yysjfjArr['flat']['yes']['big'] = 5.6;   //已满5年 140以上 差额
// yysjfjArr['other'] = 5.6;                //非住宅 差额*/

// /*//卖房 个税
// var gsArr = [];
// gsArr['flat'] = 5;
// gsArr['other'] = 555;*/

// //卖房 印花税
// var sellyhsArr = [];
// sellyhsArr['flat'] = 0;
// sellyhsArr['other'] = 0.05;

// //卖房 土地增值税
// var selltdzzsArr = [];
// selltdzzsArr['flat'] = 0;                      //住宅
// selltdzzsArr['other'] = 0.5;                   //非住宅
		
// //卖房 产权交易手续费
// var sellcqjysxfArr = [];
// sellcqjysxfArr['flat'] = [];
// sellcqjysxfArr['flat']['normal'] = 0;    //140平以下
// sellcqjysxfArr['flat']['big'] = 3;       //140以上 3元/平方米
// /*sellcqjysxfArr['flat']['no'] = [];             //5年以内
// sellcqjysxfArr['flat']['no']['normal'] = 0;    //140平以下
// sellcqjysxfArr['flat']['no']['big'] = 3;       //140以上 3元/平方米
// sellcqjysxfArr['flat']['yes'] = [];            //5年以上
// sellcqjysxfArr['flat']['yes']['normal'] = 0;   //140平以下
// sellcqjysxfArr['flat']['yes']['big'] = 3;      //140以上 3元/平方米*/
// sellcqjysxfArr['other'] = 6;

// //卖房 土地交易手续费
// var selltdjysxfArr = [];
// selltdjysxfArr['flat'] = [];
// selltdjysxfArr['flat']['normal'] = 0;
// selltdjysxfArr['flat']['big'] = 50;
// selltdjysxfArr['other'] = 0;

// var selltax,selltaxbase,persontax,persontaxbase,sellyhs,sellcqjysxf,selltdzzs,selltdjysxf;
// //dwk 卖房点击填写 2010年9月30日
// function sellselectok(id){
// 	var e = getevent();
// 	var o = e.srcElement || e.target;
// 	if(o.getAttribute('val') != undefined){
// 		document.getElementById(id).value = o.innerHTML;
// 		var type = o.getAttribute('type');
// 		var space = o.getAttribute('space');
// 		var base = o.getAttribute('base');
// 		if(type == 'other'){
// 			sellcqjysxf = sellcqjysxfArr[type];
// 			selltdjysxf = selltdjysxfArr[type];
// 		}else{
// 			sellcqjysxf = sellcqjysxfArr[type][space];
// 			selltdjysxf = selltdjysxfArr[type][space];
// 		}
// 		if(base == 'selldiff'){
// 			selltaxbase = 'diff';
// 			selltax = o.getAttribute('val');
// 		}else if(base == 'selltotal'){
// 			selltaxbase = 'total';
// 			selltax = o.getAttribute('val');
// 		}else if(base == 'persondiff'){
// 			persontaxbase = 'diff';
// 			persontax = o.getAttribute('val');
// 		}else if(base == 'persontotal'){
// 			persontaxbase = 'total';
// 			persontax = o.getAttribute('val');
// 		}
		
// 		sellyhs = sellyhsArr[type];
// 		selltdzzs = selltdzzsArr[type];
		
// 		closedetail(id + 'list');
// 	}
// }
// //dwk 税费计算 卖房 2010年9月30日
// function sell(){
// 	var space = document.getElementById('sellspace').value;
// 	if(!reg_Num(space)){
// 		alert('请填写面积！');
// 		document.getElementById('sellspace').focus();
// 		return false;
// 	}
// 	var buyprice = document.getElementById('buyprice').value;
// 	if(!reg_Num(buyprice)){
// 		alert('请填写买入总价！');
// 		document.getElementById('buyprice').focus();
// 		return false;
// 	}
// 	var sellprice = document.getElementById('sellprice').value;
// 	if(!reg_Num(sellprice)){
// 		alert('请填写卖出总价！');
// 		document.getElementById('sellprice').focus();
// 		return false;
// 	}
	
// 	if(selltax==undefined || !reg_Num(selltax)){
// 		alert('请选择营业税！');
// 		showdetail('selltaxdetaillist');
// 		return false;
// 	}
// 	if(persontax==undefined || !reg_Num(persontax)){
// 		alert('请选择个税！');
// 		showdetail('persontaxdetaillist');
// 		return false;
// 	}
	
// 	var diff = (sellprice - buyprice) * 10000;
// 	if(selltaxbase == 'diff'){
// 		var allselltax = Math.round((selltax/100) * diff);
// 	}else if(selltaxbase == 'total'){
// 		var allselltax = Math.round((selltax/100) * (sellprice * 10000));
// 	}
// 	if(persontaxbase == 'diff'){
// 		var allpersontax = Math.round((persontax/100) * diff);
// 	}else if(persontaxbase == 'total'){
// 		var allpersontax = Math.round((persontax/100) * (sellprice * 10000));
// 	}

	document.getElementById('selltax').innerHTML = allselltax + '元';
	document.getElementById('persontax').innerHTML = allpersontax + '元';
	var allsellyhs = Math.round((sellyhs/100)*(sellprice*10000));
	document.getElementById('sellyhs').innerHTML = allsellyhs + '元';
	
	var allselltdzzs = Math.round((selltdzzs/100)*(sellprice*10000));
	document.getElementById('selltdzzs').innerHTML = allselltdzzs + '元';
	var allsellcqjysxf = Math.round((sellcqjysxf/100)*space);
	document.getElementById('sellcqjysxf').innerHTML = allsellcqjysxf + '元';

	document.getElementById('selltdjysxf').innerHTML = selltdjysxf + '元';
	var sellzj = allselltax+allpersontax+allsellyhs+allselltdzzs+allsellcqjysxf+selltdjysxf;
	document.getElementById('sellzj').innerHTML = sellzj;
}
window.onload = function(){
	if(document.location.search){
		var param = document.location.search;
		param = param.replace(/(^\?+)|(\?+$)|(^\-+)|(\-+$)/g, '');
		param = param.replace(/(\-{2,})/g, '-');
		var param = param.split('-');
		var l = param.length;
		
		if(l==1 && param[0]=='g'){
			document.getElementById('debxloan').value = 2;
			document.getElementById('debjloan').value = 2;
		}else if(l==2){
			setTab(param[0],param[1]);
		}else if(l>3){
			setTab(param[0],param[1]);
			setTab(param[2],param[3]);
		}
	}
};