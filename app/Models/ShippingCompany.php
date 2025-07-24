<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ShippingCompany extends Model
{
    use HasFactory;

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'code',
        'description',
        'website',
        'tracking_url_template',
        'logo',
        'contact_person',
        'contact_email',
        'contact_phone',
        'has_api_integration',
        'api_credentials',
        'is_active',
    ];

    /**
     * Los atributos que deben ser convertidos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'has_api_integration' => 'boolean',
        'is_active' => 'boolean',
        'api_credentials' => 'encrypted:array',
    ];

    /**
     * Obtener los envíos asociados a esta empresa.
     */
    public function shipments()
    {
        return $this->hasMany(Shipment::class);
    }

    /**
     * Crear un envío a través de la API de la empresa de transporte.
     *
     * @param array $shipmentData
     * @return array
     */
    public function createShipmentViaApi(array $shipmentData)
    {
        if (!$this->has_api_integration) {
            return [
                'success' => false,
                'message' => 'Esta empresa no tiene integración API',
                'data' => null
            ];
        }

        switch ($this->code) {
            case 'aramex':
                return $this->createAramexShipment($shipmentData);
            case 'zajil':
                return $this->createZajilShipment($shipmentData);
            case 'bosta':
                return $this->createBostaShipment($shipmentData);
            default:
                return [
                    'success' => false,
                    'message' => 'No se encontró integración para esta empresa',
                    'data' => null
                ];
        }
    }

    /**
     * Crear un envío a través de la API de Aramex.
     *
     * @param array $data
     * @return array
     */
    protected function createAramexShipment(array $data)
    {
        try {
            $credentials = $this->api_credentials;
            
            $payload = [
                'ClientInfo' => [
                    'UserName' => $credentials['username'] ?? '',
                    'Password' => $credentials['password'] ?? '',
                    'Version' => $credentials['version'] ?? 'v1',
                    'AccountNumber' => $credentials['account_number'] ?? '',
                    'AccountPin' => $credentials['account_pin'] ?? '',
                    'AccountEntity' => $credentials['account_entity'] ?? '',
                    'AccountCountryCode' => $credentials['account_country_code'] ?? '',
                ],
                'Shipments' => [
                    [
                        'Reference1' => $data['order_number'],
                        'Reference2' => '',
                        'Reference3' => '',
                        'Shipper' => [
                            'Reference1' => '',
                            'Reference2' => '',
                            'AccountNumber' => $credentials['account_number'] ?? '',
                            'PartyAddress' => [
                                'Line1' => $data['sender_address_line1'],
                                'Line2' => $data['sender_address_line2'] ?? '',
                                'Line3' => '',
                                'City' => $data['sender_city'],
                                'StateOrProvinceCode' => $data['sender_state'],
                                'PostCode' => $data['sender_postal_code'],
                                'CountryCode' => $data['sender_country_code'],
                            ],
                            'Contact' => [
                                'Department' => '',
                                'PersonName' => $data['sender_name'],
                                'Title' => '',
                                'CompanyName' => $data['sender_company'] ?? '',
                                'PhoneNumber1' => $data['sender_phone'],
                                'PhoneNumber1Ext' => '',
                                'PhoneNumber2' => '',
                                'PhoneNumber2Ext' => '',
                                'FaxNumber' => '',
                                'CellPhone' => $data['sender_phone'],
                                'EmailAddress' => $data['sender_email'] ?? '',
                            ],
                        ],
                        'Consignee' => [
                            'Reference1' => '',
                            'Reference2' => '',
                            'AccountNumber' => '',
                            'PartyAddress' => [
                                'Line1' => $data['receiver_address_line1'],
                                'Line2' => $data['receiver_address_line2'] ?? '',
                                'Line3' => '',
                                'City' => $data['receiver_city'],
                                'StateOrProvinceCode' => $data['receiver_state'],
                                'PostCode' => $data['receiver_postal_code'],
                                'CountryCode' => $data['receiver_country_code'],
                            ],
                            'Contact' => [
                                'Department' => '',
                                'PersonName' => $data['receiver_name'],
                                'Title' => '',
                                'CompanyName' => '',
                                'PhoneNumber1' => $data['receiver_phone'],
                                'PhoneNumber1Ext' => '',
                                'PhoneNumber2' => '',
                                'PhoneNumber2Ext' => '',
                                'FaxNumber' => '',
                                'CellPhone' => $data['receiver_phone'],
                                'EmailAddress' => $data['receiver_email'] ?? '',
                            ],
                        ],
                        'ShippingDateTime' => now()->format('Y-m-d\TH:i:s'),
                        'DueDate' => now()->addDays(1)->format('Y-m-d\TH:i:s'),
                        'Comments' => $data['notes'] ?? '',
                        'PickupLocation' => '',
                        'OperationsInstructions' => '',
                        'Details' => [
                            'Dimensions' => [
                                'Length' => $data['length'] ?? 10,
                                'Width' => $data['width'] ?? 10,
                                'Height' => $data['height'] ?? 10,
                                'Unit' => 'cm',
                            ],
                            'ActualWeight' => [
                                'Unit' => 'kg',
                                'Value' => $data['weight'] ?? 1,
                            ],
                            'ProductGroup' => 'EXP',
                            'ProductType' => 'PPX',
                            'PaymentType' => $data['is_cod'] ? 'COD' : 'P',
                            'PaymentOptions' => '',
                            'NumberOfPieces' => $data['quantity'] ?? 1,
                            'DescriptionOfGoods' => $data['description'] ?? 'Goods',
                            'GoodsOriginCountry' => $data['sender_country_code'],
                            'CashOnDeliveryAmount' => [
                                'Value' => $data['is_cod'] ? $data['cod_amount'] : 0,
                                'CurrencyCode' => $data['currency'] ?? 'SAR',
                            ],
                        ],
                    ],
                ],
            ];

            // Realizar la solicitud a la API de Aramex
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($credentials['api_url'] ?? 'https://ws.aramex.net/ShippingAPI.V2/Shipping/Service_1_0.svc/json/CreateShipments', $payload);

            $responseData = $response->json();

            if (isset($responseData['Shipments'][0]['ID']) && !empty($responseData['Shipments'][0]['ID'])) {
                return [
                    'success' => true,
                    'message' => 'Envío creado con éxito en Aramex',
                    'data' => [
                        'tracking_number' => $responseData['Shipments'][0]['ID'],
                        'tracking_url' => str_replace('{tracking_number}', $responseData['Shipments'][0]['ID'], $this->tracking_url_template),
                        'label_url' => $responseData['Shipments'][0]['ShipmentLabel']['LabelURL'] ?? null,
                    ],
                ];
            }

            return [
                'success' => false,
                'message' => 'Error al crear envío en Aramex: ' . ($responseData['HasErrors'] ? json_encode($responseData['Notifications']) : 'Error desconocido'),
                'data' => $responseData
            ];
        } catch (\Exception $e) {
            Log::error('Error al crear envío en Aramex: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al crear envío en Aramex: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Crear un envío a través de la API de Zajil.
     *
     * @param array $data
     * @return array
     */
    protected function createZajilShipment(array $data)
    {
        try {
            $credentials = $this->api_credentials;
            
            $payload = [
                "PaymentTypeId" => 83, // Prepaid
                "SenderPhoneCode" => substr($data['sender_phone'], 0, 5),
                "SenderPhoneNumber" => substr($data['sender_phone'], 5),
                "SenderName" => $data['sender_name'],
                "SenderCity" => $data['sender_city_id'], // ID de ciudad según Zajil
                "SenderAddress" => $data['sender_address_line1'],
                "ReceiverPhoneNumber" => substr($data['receiver_phone'], 5),
                "ReceiverName" => $data['receiver_name'],
                "ReceiverAddress" => $data['receiver_address_line1'],
                "ReceiverCity" => $data['receiver_city_id'], // ID de ciudad según Zajil
                "ReceiverRegionId" => $data['receiver_region_id'], // ID de región según Zajil
                "ReceiverPhonecode" => substr($data['receiver_phone'], 0, 5),
                "DestinationBranchId" => $data['destination_branch_id'] ?? 181, // Branch ID según Zajil
                "TotalPieces" => $data['quantity'] ?? 1,
                "WeighingUnitId" => 41, // Por defecto
                "VolumtricWeightList" => [0.2],
                "ActualWeightList" => [$data['weight'] ?? 1],
                "NetAmount" => $data['is_cod'] ? $data['cod_amount'] : 0,
                "AmountToBePaid" => $data['is_cod'] ? $data['cod_amount'] : 0,
                "ActualWeight" => $data['weight'] ?? 1,
                "ChargeableWeight" => $data['weight'] ?? 1,
                "TotalLength" => $data['length'] ?? 10,
                "TotalWidth" => $data['width'] ?? 10,
                "TotalHeight" => $data['height'] ?? 10,
                "ShipmentValue" => $data['shipment_value'] ?? 0,
                "ShipmentDescription" => $data['description'] ?? 'Goods'
            ];

            // Realizar la solicitud a la API de Zajil
            $response = Http::withHeaders([
                'Authorization' => $credentials['api_key'] ?? '',
                'Content-Type' => 'application/json',
            ])->post($credentials['api_url'] ?? 'https://staging.zajil-express.org/api/pudoshipment/create', $payload);

            $responseData = $response->json();

            if (isset($responseData['result']['status']) && $responseData['result']['status'] === 'SUCCESS') {
                return [
                    'success' => true,
                    'message' => 'Envío creado con éxito en Zajil',
                    'data' => [
                        'tracking_number' => $responseData['result']['refNo'],
                        'tracking_url' => 'https://mobileapi.zajil-express.org/track?shipment=' . $responseData['result']['refNo'],
                    ],
                ];
            }

            return [
                'success' => false,
                'message' => 'Error al crear envío en Zajil: ' . ($responseData['result']['message'] ?? 'Error desconocido'),
                'data' => $responseData
            ];
        } catch (\Exception $e) {
            Log::error('Error al crear envío en Zajil: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al crear envío en Zajil: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }
    
    /**
     * Crear un envío a través de la API de Bosta.
     *
     * @param array $data
     * @return array
     */
    protected function createBostaShipment(array $data)
    {
        try {
            $credentials = $this->api_credentials;
            
            // Preparar el receptor para Bosta
            $receiver = [
                'firstName' => explode(' ', $data['receiver_name'])[0],
                'lastName' => count(explode(' ', $data['receiver_name'])) > 1 ? explode(' ', $data['receiver_name'])[1] : '',
                'phone' => $data['receiver_phone'],
            ];
            
            if (!empty($data['receiver_email'])) {
                $receiver['email'] = $data['receiver_email'];
            }
            
            // Preparar la dirección de entrega para Bosta
            $dropOffAddress = [
                'firstLine' => $data['receiver_address_line1'],
                'city' => $data['receiver_city_code'] ?? 'EG-01', // Código de ciudad según Bosta
            ];
            
            if (!empty($data['receiver_address_line2'])) {
                $dropOffAddress['secondLine'] = $data['receiver_address_line2'];
            }
            
            if (!empty($data['receiver_zone'])) {
                $dropOffAddress['zone'] = $data['receiver_zone'];
            }
            
            // Payload para la API de Bosta
            $payload = [
                'type' => $data['is_cod'] ? 20 : 10, // 10: Entrega de paquete, 20: Cobro contra entrega
                'specs' => [
                    'packageDetails' => [
                        'itemsCount' => $data['quantity'] ?? 1,
                        'weight' => $data['weight'] ?? 1,
                    ]
                ],
                'dropOffAddress' => $dropOffAddress,
                'receiver' => $receiver,
            ];
            
            // Agregar campos opcionales
            if (!empty($data['notes'])) {
                $payload['notes'] = $data['notes'];
            }
            
            if (!empty($data['order_number'])) {
                $payload['businessReference'] = $data['order_number'];
            }
            
            // Si es un envío con cobro contra entrega
            if ($data['is_cod']) {
                $payload['cod'] = $data['cod_amount'];
            }
            
            // Realizar la solicitud a la API de Bosta
            $response = Http::withHeaders([
                'Authorization' => $credentials['api_key'] ?? '',
                'Content-Type' => 'application/json',
            ])->post($credentials['api_url'] ?? 'https://app.bosta.co/api/v0/deliveries', $payload);

            $responseData = $response->json();

            if (isset($responseData['_id']) && !empty($responseData['_id'])) {
                return [
                    'success' => true,
                    'message' => 'Envío creado con éxito en Bosta',
                    'data' => [
                        'tracking_number' => $responseData['trackingNumber'],
                        'tracking_url' => str_replace('{tracking_number}', $responseData['trackingNumber'], $this->tracking_url_template),
                        'delivery_id' => $responseData['_id'],
                    ],
                ];
            }

            return [
                'success' => false,
                'message' => 'Error al crear envío en Bosta: ' . ($responseData['message'] ?? 'Error desconocido'),
                'data' => $responseData
            ];
        } catch (\Exception $e) {
            Log::error('Error al crear envío en Bosta: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al crear envío en Bosta: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Rastrear un envío a través de la API de la empresa.
     *
     * @param string $trackingNumber
     * @return array
     */
    public function trackShipmentViaApi(string $trackingNumber)
    {
        if (!$this->has_api_integration) {
            return [
                'success' => false,
                'message' => 'Esta empresa no tiene integración API',
                'data' => null
            ];
        }

        switch ($this->code) {
            case 'aramex':
                return $this->trackAramexShipment($trackingNumber);
            case 'zajil':
                return $this->trackZajilShipment($trackingNumber);
            case 'bosta':
                return $this->trackBostaShipment($trackingNumber);
            default:
                return [
                    'success' => false,
                    'message' => 'No se encontró integración para esta empresa',
                    'data' => null
                ];
        }
    }

    /**
     * Rastrear un envío de Aramex.
     *
     * @param string $trackingNumber
     * @return array
     */
    protected function trackAramexShipment(string $trackingNumber)
    {
        try {
            $credentials = $this->api_credentials;
            
            $payload = [
                'ClientInfo' => [
                    'UserName' => $credentials['username'] ?? '',
                    'Password' => $credentials['password'] ?? '',
                    'Version' => $credentials['version'] ?? 'v1',
                    'AccountNumber' => $credentials['account_number'] ?? '',
                    'AccountPin' => $credentials['account_pin'] ?? '',
                    'AccountEntity' => $credentials['account_entity'] ?? '',
                    'AccountCountryCode' => $credentials['account_country_code'] ?? '',
                ],
                'Shipments' => [
                    $trackingNumber
                ],
                'GetLastTrackingUpdateOnly' => false,
            ];

            // Realizar la solicitud a la API de Aramex
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($credentials['tracking_api_url'] ?? 'https://ws.aramex.net/ShippingAPI.V2/Tracking/Service_1_0.svc/json/TrackShipments', $payload);

            $responseData = $response->json();

            if (isset($responseData['TrackingResults']) && !empty($responseData['TrackingResults'])) {
                return [
                    'success' => true,
                    'message' => 'Información de seguimiento obtenida con éxito',
                    'data' => $responseData['TrackingResults'],
                ];
            }

            return [
                'success' => false,
                'message' => 'Error al obtener información de seguimiento: ' . ($responseData['HasErrors'] ? json_encode($responseData['Notifications']) : 'Error desconocido'),
                'data' => $responseData
            ];
        } catch (\Exception $e) {
            Log::error('Error al rastrear envío de Aramex: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al rastrear envío de Aramex: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Rastrear un envío de Zajil.
     *
     * @param string $trackingNumber
     * @return array
     */
    protected function trackZajilShipment(string $trackingNumber)
    {
        try {
            $trackingUrl = "https://mobileapi.zajil-express.org/track?shipment={$trackingNumber}";
            
            // Realizar la solicitud a la API de Zajil
            $response = Http::get($trackingUrl);
            $responseData = $response->json();

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Información de seguimiento obtenida con éxito',
                    'data' => $responseData,
                ];
            }

            return [
                'success' => false,
                'message' => 'Error al obtener información de seguimiento de Zajil',
                'data' => $responseData
            ];
        } catch (\Exception $e) {
            Log::error('Error al rastrear envío de Zajil: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al rastrear envío de Zajil: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }
    
    /**
     * Rastrear un envío de Bosta.
     *
     * @param string $trackingNumber
     * @return array
     */
    protected function trackBostaShipment(string $trackingNumber)
    {
        try {
            $credentials = $this->api_credentials;
            
            // Realizar la solicitud a la API de Bosta
            $response = Http::withHeaders([
                'Authorization' => $credentials['api_key'] ?? '',
                'Content-Type' => 'application/json',
            ])->get($credentials['api_base_url'] ?? 'https://app.bosta.co/api/v0/deliveries/tracking/' . $trackingNumber);

            $responseData = $response->json();

            if ($response->successful() && isset($responseData['trackingNumber'])) {
                return [
                    'success' => true,
                    'message' => 'Información de seguimiento obtenida con éxito',
                    'data' => $responseData,
                ];
            }

            return [
                'success' => false,
                'message' => 'Error al obtener información de seguimiento de Bosta: ' . ($responseData['message'] ?? 'Error desconocido'),
                'data' => $responseData
            ];
        } catch (\Exception $e) {
            Log::error('Error al rastrear envío de Bosta: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al rastrear envío de Bosta: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }
}
