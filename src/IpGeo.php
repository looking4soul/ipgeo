<?php
namespace Looking4soul\IpGeo;

class IpGeo
{
	private $redis;

	public function __construct()
	{
		$this->redis = new \Predis\Client('tcp://127.0.0.1:6379');
	}

	public function import($block, $location)
	{
		$this->import_ips_to_redis($block);
		$this->import_cities_to_redis($location);

	}

	private function import_ips_to_redis($block)
	{
		$csv = \League\Csv\Reader::createFromPath($block);
		foreach ($csv as $index => $row) {
			$startIp = explode('/', $row[0])[0];
			if (!preg_match('/.+\..+\..+\..+/', $startIp)) {
				continue;
			}
			$startIpScore = $this->ip_to_score($startIp);

			if (empty($row[1])) continue;
			$cityId = $row[1] . '_' . $index;

			$this->redis->zadd('ip2cityid:', $startIpScore, $cityId);
		}
	}
	
	private function import_cities_to_redis($location)
	{
		$csv = \League\Csv\Reader::createFromPath($location);
		foreach ($csv as $index => $row) {
			$cityId = $row[0];
			$cityName = $row[10];
			$countryName = $row[5];
			$info = json_encode(array($cityName, $countryName));
			$this->redis->hset("cityid2city:", $cityId, $info);
		}
	}

	public function find_city_by_ip($ip)
	{
		$ipScore = $this->ip_to_score($ip);
		$cityId = $this->redis->zrevrangebyscore('ip2cityid:', $ipScore, 0, 'limit', 0, 1);
		if (empty($cityId)) return null;
		$cityId = explode("_", $cityId[0])[0];
		$info = $this->redis->hget("cityid2city:", $cityId);
		return json_decode($info);
	}

	private function ip_to_score($ip)
	{
		$score = 0;
		foreach (explode('.', $ip) as $v) {
			$score = $score * 256 + (int)$v;
		}
		return $score;
	}
}
