<?php

namespace Talis\TrickPlayBundle\Service;
use Symfony\Component\DomCrawler\Crawler;

class Lodestone
{
  const LODESTONE_URL = "http://na.finalfantasyxiv.com/lodestone";
  const WORLD = "Gilgamesh";
  const MEMBERS_PER_PAGE = 20;

  // Searches for companies
  // Return a max. of 5 results
  public function searchFreeCompanies($name)
  {
    $url = "/freecompany/?q=" . urlencode($name) . "&worldname=" . urlencode(self::WORLD);
    return self::parseFreeCompanySearch(self::getWebpage($url));
  }

  // Return free company data
  public function getFreeCompany($id)
  {
    $url = "/freecompany/" . urlencode($id);
    $company = self::parseFreeCompany(self::getWebpage($url));
    $pages = round($company["size"] / self::MEMBERS_PER_PAGE, 0, PHP_ROUND_HALF_UP);
    $company["members"] = [];
    $company["id"] = $id;

    // Load members
    for ($i = 0; $i < $pages; ++ $i)
    {
      $url = "/freecompany/" . urlencode($id) . "/member?page=" . urlencode($i + 1);
      $company["members"] = array_merge($company["members"], self::parseCharacterSearch(self::getWebpage($url)));
    }

    return $company;
  }

  // Searches for characters
  // Return a max. of 5 results
  public function searchCharacters($name)
  {
    $url = "/character?q=" . urlencode($name) . "&worldname=" . urlencode(self::WORLD);
    return self::parseCharacterSearch(self::getWebpage($url));
  }

  // Return character data
  public function getCharacter($id)
  {
    $url = "/character/" . urlencode($id);
    $character = self::parseCharacter(self::getWebpage($url));
    $character["id"] = $id;
    return $character;
  }

  // Parses character page
  private static function parseCharacter($html)
  {
    $crawler = new Crawler($html);
    $levels = $crawler->filter(".base_inner table.class_list tr");
    $result = array();

    $levels->each(function($node) use (&$result)
    {
      $columns = $node->filter("td");

      // Left column
      $name = $columns->first()->text();
      $level = $columns->eq(1)->text();
      $result[$name] = ($level == "-" || $level == "1") ? null : $level;

      // Right column
      if (count($columns) < 5) return;
      $name = $columns->eq(3)->text();
      $level = $columns->eq(4)->text();
      $result[$name] = ($level == "-" || $level == "1") ? null : $level;
    });

    return $result;
  }

  // Parses free company page
  private static function parseFreeCompany($html)
  {
    $crawler = new Crawler($html);
    $rows = $crawler->filter(".base_body table.table_style2 tr");
    $picture = $crawler->filter(".ic_freecompany_box img")->first()->attr("src");

    return array(
      "picture" => $picture,
      "name" => $rows->eq(0)->filter("td.vm span.txt_yellow")->first()->text(),
      "tag" => preg_replace("/((.*)«)|(»(.*)$)/", "", $rows->eq(0)->filter("td.vm")->first()->text()),
      "size" => $rows->eq(2)->filter("td")->first()->text(),
      "slogan" => $rows->eq(3)->filter("td")->first()->text()
    );
  }

  // Parses character search result page
  private static function parseCharacterSearch($html)
  {
    $crawler = new Crawler($html);
    $characters = $crawler->filter(".base_body .table_black_border_bottom table tr");
    $result = array();

    $characters->each(function($node) use (&$result)
    {
      $picture = $node->filter("th .thumb_cont_black_50 img")->first();
      $name = $node->filter("td .player_name_gold a")->first();

      $result[] = array(
        "picture" => $picture->attr("src"),
        "name" => $name->text(),
        "id" => rtrim(str_replace("/lodestone/character/", "", $name->attr("href")), "/")
      );
    });

    return $result;
  }

  // Parses free company search result page
  private static function parseFreeCompanySearch($html)
  {
    $crawler = new Crawler($html);
    $characters = $crawler->filter(".base_body .table_black_border_bottom table tr");
    $result = array();

    $characters->each(function($node) use (&$result)
    {
      $picture = $node->filter("th .ic_freecompany_box img")->first();
      $name = $node->filter("td .player_name_gold a")->first();

      $result[] = array(
        "picture" => $picture->attr("src"),
        "name" => $name->text(),
        "id" => rtrim(str_replace("/lodestone/freecompany/", "", $name->attr("href")), "/")
      );
    });

    return $result;
  }

  // Return webpage HTML content
  private static function getWebpage($url)
  {
      $options = array(
          CURLOPT_URL => self::LODESTONE_URL . $url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_TIMEOUT => 10
      );

      $curl = curl_init();
      curl_setopt_array($curl, $options);
      $response = curl_exec($curl);
      curl_close($curl);

      return $response;
  }
}
