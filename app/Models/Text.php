<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Text extends Model
{
    public function importanceTerms()
    {
        return $this->hasMany('App\Models\ImportanceTerm')->orderByDesc('tfidf');
    }

    public function setImportanceTerms()
    {
        //名詞の抽出と重要度の算出
        $frequencyAndTfs =  $this->generateFrequencyAndTfs();

        //DBに保存
        foreach ($frequencyAndTfs as $key => $frequencyAndTf) {
            $importanceTerm = new \App\Models\ImportanceTerm();
            $importanceTerm->term = $key;
            $importanceTerm->tf = $frequencyAndTf['tf'];
            $importanceTerm->frequency = $frequencyAndTf['frequency'];
            $this->importanceTerms()->save($importanceTerm);
        }

    }

    private function generateFrequencyAndTfs() : array
    {

        //文字列を解析
        $mecab = new \Mecab\Tagger();
        $nodes = $mecab->parseToNode($this->text);

        //形態素ごとに名詞かどうか、重要度はいくつかを算出
        $allTerms = array();
        $terms = array();
        $compoundNoun = '';

        foreach ($nodes as $n) {

            $result = explode(',', $n->getFeature());

            //空白は無視
            if($n->getSurface() == '')
                continue;

            //全単語の頻出回数を記録
            $this->incrementFrequency($allTerms, $n->getSurface());

            //名詞ではない かつ 前も名詞ではない場合はスキップ
            if($compoundNoun == '' && $result[0] != '名詞'){
                continue;

            //名詞ではない かつ 複合名詞が空でない場合は、複合名詞としてカウント
            }else if($compoundNoun != '' && $result[0] != '名詞'){

                //ひらがな１文字は除外する
                if(preg_match('/^[ぁ-ん]$/u', $compoundNoun)){
                    $compoundNoun = '';
                    continue;
                }

                //複合名詞がまだ単名詞の場合は除外する
                if($compoundNoun == $n->getSurface()){
                    $compoundNoun = '';
                    continue;
                }

                //複合名詞を格納
                $this->incrementFrequency($terms, $compoundNoun);
                $compoundNoun = '';

            //名詞 かつ 前の形態素も名詞の場合
            }else if($compoundNoun != '' && $result[0] == '名詞'){

                //前の名詞と複合名詞が一致する場合、前の名詞を単名詞としてカウント
                if($compoundNoun == $n->getPrev()->getSurface()){
                    $this->incrementFrequency($terms, $n->getPrev()->getSurface());
                }

                $this->incrementFrequency($terms, $n->getSurface());
                $compoundNoun .= $n->getSurface();

            //名詞 かつ 最初の出現の場合
            }else{
                $compoundNoun .= $n->getSurface();
            }
        }

        $frequencyAndTfs = array();
        $sumFrequency = array_sum($allTerms);
        foreach ($terms as $term => $value) {
            $frequencyAndTfs[$term] = ['frequency'=> $value
                                       , 'tf' => $value / $sumFrequency];
        }

        return $frequencyAndTfs;
    }

    private function incrementFrequency(&$terms, $term)
    {
        isset($terms[$term]) ? $terms[$term]++ : $terms[$term] = 1;
    }
}
