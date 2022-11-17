<?php
use PhpCfdi\SatWsDescargaMasiva\PackageReader\CfdiPackageReader;
use PhpCfdi\SatWsDescargaMasiva\PackageReader\MetadataPackageReader;
use PhpCfdi\SatWsDescargaMasiva\Service;
use PhpCfdi\SatWsDescargaMasiva\Services\Query\QueryParameters;
use PhpCfdi\SatWsDescargaMasiva\Shared\DateTime;
use PhpCfdi\SatWsDescargaMasiva\Shared\DateTimePeriod;
use PhpCfdi\SatWsDescargaMasiva\Shared\DownloadType;
use PhpCfdi\SatWsDescargaMasiva\Shared\Fiel;
use PhpCfdi\SatWsDescargaMasiva\Shared\RequestType;
use PhpCfdi\SatWsDescargaMasiva\WebClient\GuzzleWebClient;

// Creación de la fiel, puede leer archivos DER (como los envía el SAT) o PEM (convertidos)
$fiel = Fiel::create(
    file_get_contents('certificado.cer'),
    file_get_contents('llaveprivada.key'),
    '12345678a'
);

// verificar que la fiel sea válida (no sea CSD y sea vigente acorde a la fecha del sistema)
if (! $fiel->isValid()) {
    return;
}

// creación del web client basado en Guzzle que implementa WebClientInterface
// para usarlo necesitas instalar guzzlehttp/guzzle pues no es una dependencia directa
$webClient = new GuzzleWebClient();

// Creación del servicio
$service = new Service($fiel, $webClient);

// presentar una solicitud
$request = new QueryParameters(
    new DateTimePeriod(new DateTime('2019-01-13 00:00:00'), new DateTime('2019-01-13 23:59:59')),
    DownloadType::issued(),
    RequestType::metadata()
);
$query = $service->query($request);
$requestId = $query->getRequestId();

// consultar el servicio de verificación
$verify = $service->verify($requestId);
$packageId = $verify->getPackagesIds()[0];

// descargar
$download = $service->download($packageId);
$zipfile = "$packageId.zip";
file_put_contents($zipfile, $download->getPackageContent());

// obtener los CFDI del archivo ZIP
$cfdiReader = new CfdiPackageReader($zipfile);
foreach ($cfdiReader->fileContents() as $name => $content) {
    file_put_contents("cfdis/$name", $content);
}

// y si el contenido fuera un metadata
$metadataReader = new MetadataPackageReader($zipfile);
foreach ($metadataReader->metadata() as $metadata) {
    echo $metadata->uuid, PHP_EOL;
}