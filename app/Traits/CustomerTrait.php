<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash as FacadesHash;

trait CustomerTrait
{

    private function prepareCustomerData($request)
    {
        $companyDetails = DB::table('company_profile')->where('user_id', auth()->id())->get();
        $customerData = [
            'firstName' => $request->input('first_name'),
            'lastName' => $request->input('last_name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'taxId' => $request->input('tax_id'),
            'taxPercentage' => $request->input('tax_percentage'),
            'zone' => $request->input('zone'),
            'phoneType' => $request->input('phone_type'),
            'phoneNote' => $request->input('phone_note'),
            'company' => $request->input('company'),
            'customerType' => $request->input('customer_type'),
            'enableCustomerAccountType' => $request->input('enable_customer_account_type'),
            'address' => $request->input('address'),
            'addressExplode' => explode(",", $request->input('address')),
            'address' => explode(",", $request->input('address'))[0],
            'streetNo' => explode(' ', explode(",", $request->input('address'))[0]),
            'streetNo' => explode(' ', explode(",", $request->input('address'))[0])[0],
            'sideMark' => $request->input('first_name') . "-" . explode(' ', explode(",", $request->input('address'))[0])[0],
            'city' => $request->input('city'),
            'orderPrefix' => $request->input('order_prefix'),
            'state' => $request->input('state'),
            'zipCode' => $request->input('zip'),
            'countryCode' => $request->input('country_code'),
            'fileUpload' => $request->input('file_upload'),
            'username' => $request->input('username'),
            'password' => $request->input('password'),
            'paymentTerm' => $request->input('payment_term'),
            'paymentTermType' => $request->input('payment_term_type'),
            'isTaxable' => $request->input('is_taxable'),
            'differentShippingAddress' => $request->input('different_shipping_address'),
            'enableShippingZone' => $request->input('enable_shipping_zone'),

            // Shipping address info start
            'shippingFirstName' => $request->input('shipping_first_name'),
            'shippingLastName' => $request->input('shipping_last_name'),
            'shippingEmail' => $request->input('shipping_email'),
            'shippingPhone' => $request->input('shipping_phone'),
            'shippingAddress_' => $request->input('shipping_address'),
            'shippingAddressExplode' => explode(",", $request->input('shipping_address')),
            'shippingAddress' => explode(",", $request->input('shipping_address'))[0],
            'shippingCity' => $request->input('shipping_city'),
            'shippingState' => $request->input('shipping_state'),
            // 'isResidential' => $request->input('is_residential'),
            // 'commercial' => $request->input('commercial'),
            // 'storageFacility' => $request->input('storage_facility'),
            // 'freightTerminal' => $request->input('freight_terminal'),
            'shippingZip' => $request->input('shipping_zip'),
            'shippingCountryCode' => $request->input('shipping_country_code'),
            'billingAddressLabel' => $request->input('billing_address_label'),
            'isResidential' => ($request->input('shipping_address_label') == "is_residential") ? 1 : 0,
            'commercial' => ($request->input('shipping_address_label') == "commercial") ? 1 : 0,
            'storageFacility' => ($request->input('shipping_address_label') == "storage_facility") ? 1 : 0,
            'freightTerminal' => ($request->input('shipping_address_label') == "freight_terminal") ? 1 : 0,
            'singleHouse' => ($request->input('shipping_address_label') == "single_house") ? 1 : 0,
            'condo' => ($request->input('shipping_address_label') == "condo") ? 1 : 0,
            'apartment' => ($request->input('shipping_address_label') == "apartment") ? 1 : 0,
            'pCommercial' => ($request->input('shipping_address_label') == "p_commercial") ? 1 : 0,
            'others' => ($request->input('shipping_address_label') == "others") ? 1 : 0,
            'reference' => $request->input('reference'),

            'levelId' => $this->level_id,

            // Rest of your code...
            'currency' => $companyDetails[0]->currency,
            'unit' => $companyDetails[0]->unit ?? 'inches',

        ];

        if (@$customerData['enableCustomerAccountType'] && @$customerData['customerType'] == "personal") {
            $customerData['orderPrefix'] = substr($customerData['firstName'], 0, 4);
            $customerData['company'] = $_POST['company'] = '';
        }


        $result = DB::table('b_acc_coa')
            ->where('HeadLevel', '4')
            ->where('HeadCode', 'LIKE', '1020301-%')
            ->orderByDesc('row_id')
            ->limit(1)
            ->first();

        $customerData['headcode'] = $result ? explode('-', $result->HeadCode)[0] . '-' . (explode('-', $result->HeadCode)[1] + 1) : '1020301-1';

        $lastId = DB::table('customer_info')->select('*')->orderBy('customer_id', 'desc')->first();

        $customerData['lastCustomerNo'] = $lastId->customer_no ?? "CUS-0001";
        [$prefix, $number] = explode('-', $customerData['lastCustomerNo']);
        $nextNumber = str_pad((int)$number + 1, strlen($number), '0', STR_PAD_LEFT);
        $customerData['customerNo'] = "{$prefix}-{$nextNumber}-{$customerData['firstName']} {$customerData['lastName']}";

        $cn = strtoupper(substr($customerData['company'], 0, 3)) . "-";
        $customerData['companyCustId'] = "{$cn}" . ((int)($lastId ? $lastId->customer_id : "{$cn}1") + 1);
        $customerData['customerNo'] = preg_replace("/[^a-z0-9]+/i", "-", $customerData['customerNo']);

        $customerData['responsibleEmployee'] = (!empty($request->input('responsible_employee'))) ? implode(',', $request->input('responsible_employee')) : auth()->id();

        $customerData['actionPage'] = $request->segment(2);
        $customerData['actionDone'] = "insert";
        $customerData['remarks'] = "b level Customer information save";
        $customerData['createdDate'] = now();


        return $customerData;
    }

    private function insertCustomerUserInfo($customerData)
    {
        $customerUserInfoData = [
            'created_by' => auth()->id(),
            'first_name' => $customerData['firstName'],
            'last_name' => $customerData['lastName'],
            'company' => $customerData['company'],
            'address' => $customerData['address'],
            'city' => $customerData['city'],
            'state' => $customerData['state'],
            'zip_code' => $customerData['zipCode'],
            'country_code' => $customerData['countryCode'],
            'phone' => $customerData['phone'][0],
            'email' => $customerData['email'],
            'language' => 'English',
            'user_type' => 'c',
            'wholesaler_connection' => '1',
            'create_date' => $customerData['createdDate'],
            'payment_term' => $customerData['paymentTerm'],
            'payment_term_type' => $customerData['paymentTermType'],
            'updated_by' => 0,
            'update_date' => now(),
            'active_is' => 0,
        ];
        return DB::table('user_info')->insertGetId($customerUserInfoData);
    }

    private function insertCustomerLogInfo($userInsertId, $username, $password)
    {
        $customerLogInfoData = [
            'user_id' => $userInsertId,
            'email' => $username,
            // 'password' => FacadesHash::make($password),
            'password' => md5($password),
            'user_type' => 'c',
            'is_admin' => '1',
        ];

        DB::table('log_info')->insert($customerLogInfoData);
    }

    private function insertCompanyProfile($userInsertId, $customerData)
    {

        $companyProfile = [
            'user_id' => $userInsertId,
            'company_name' => $customerData['company'],
            'email' => $customerData['email'],
            'phone' => $customerData['phone'][0],
            'address' => $customerData['address'],
            'city' => $customerData['city'],
            'state' => $customerData['state'],
            'currency' => $customerData['currency'],
            'unit' => $customerData['unit'],
            'zip_code' => $customerData['zipCode'],
            'country_code' => $customerData['countryCode'],
            'created_by' => auth()->id(),
            'created_at' => $customerData['createdDate'],
        ];
        DB::table('company_profile')->insert($companyProfile);
    }

    private function insertCustomer($userInsertId, $customerData)
    {
        $customer = [
            'customer_user_id' => $userInsertId,
            'customer_no' => $customerData['customerNo'],
            'company_customer_id' => $customerData['companyCustId'],
            'first_name' => $customerData['firstName'],
            'last_name' => $customerData['lastName'],
            'responsible_employee' => $customerData['responsibleEmployee'],
            'email' => $customerData['email'],
            'phone' => $customerData['phone'][0],
            'customer_type' => $customerData['customerType'],
            'company' => $customerData['company'],
            'address' => $customerData['address'],
            'city' => $customerData['city'],
            'state' => $customerData['state'],
            'zip_code' => $customerData['zipCode'],
            'country_code' => $customerData['countryCode'],
            'billing_address_label' => $customerData['billingAddressLabel'],
            'street_no' => $customerData['streetNo'],
            'side_mark' => $customerData['sideMark'],
            'reference' => $customerData['reference'],
            'order_prefix' => $customerData['orderPrefix'],
            'level_id' => $customerData['levelId'],
            'is_taxable' => $customerData['isTaxable'] ?? 0,
            'different_shipping_address' => $customerData['differentShippingAddress'] ?? 0,
            'enable_shipping_zone' =>  $customerData['enableShippingZone'] ?? 0,
            'created_by' => auth()->id(),
            'create_date' => now(),
        ];

        if (!empty($customerData['enableCustomerAccountType'])) {
            $customer['enable_customer_account_type'] = $customerData['enableCustomerAccountType'];
        }

        if (!empty($customerData['taxPercentage'])) {
            $customer['tax_percentage'] = $customerData['taxPercentage'];
        }

        if (!empty($customerData['zone'])) {
            $customer['zone'] = $customerData['zone'];
        }

        DB::table('customer_info')->insert($customer);
        $customer_id = DB::table('customer_info')->latest('customer_id')->value('customer_id');
        return $customer_id;

    }

    private function insertShippingAddress($userInsertId, $customerInsertedId, $customerData)
    {
        if (!empty($customerData['differentShippingAddress'])) {
            $shippingData = [
                'customer_id' => $customerInsertedId,
                'customer_user_id' => $userInsertId,
                'first_name' => $customerData['shippingFirstName'],
                'last_name' => $customerData['shippingLastName'],
                'email' => $customerData['shippingEmail'],
                'phone' => $customerData['shippingPhone'],
                'address' => $customerData['shippingAddress'],
                'city' => $customerData['shippingCity'],
                'state' => $customerData['shippingState'],
                'is_residential' => $customerData['isResidential'],
                'commercial' => $customerData['commercial'],
                'storage_facility' => $customerData['storageFacility'],
                'freight_terminal' => $customerData['freightTerminal'],
                'single_house' => $customerData['singleHouse'],
                'condo' => $customerData['condo'],
                'apartment' => $customerData['apartment'],
                'p_commercial' => $customerData['pCommercial'],
                'others' => $customerData['others'],
                'zip' => $customerData['shippingZip'],
                'country_code' => $customerData['shippingCountryCode'],
            ];

            DB::table('shipping_address_info')->insert($shippingData);
        }
    }

    private function insert_b_acc_coa($customerData)
    {
        $customerCoa = [
            'HeadCode' => $customerData['headcode'],
            'HeadName' => $customerData['customerNo'],
            'PHeadName' => 'Customer Receivable',
            'HeadLevel' => '4',
            'IsActive' => '1',
            'IsTransaction' => '1',
            'IsGL' => '0',
            'HeadType' => 'A',
            'IsBudget' => '0',
            'IsDepreciation' => '0',
            'DepreciationRate' => '0',
            'CreateBy' => auth()->id(),
            'CreateDate' => now(),
            'level_id' => $customerData['levelId'],
        ];

        DB::table('b_acc_coa')->insert($customerCoa);
    }

    private function insertCustomerPhone($userInsertId, $customerInsertedId, $customerData)
    {
        foreach (array_filter($customerData['phone']) as $i => $ph) {
            DB::table('customer_phone_type_tbl')->insert([
                'phone' => $customerData['phone'][$i],
                'phone_type' => $customerData['phoneType'][$i],
                'phone_note' => $customerData['phoneNote'][$i],
                'customer_id' => $customerInsertedId,
                'customer_user_id' => $userInsertId,
            ]);
        }
    }

    private function insertCustomerTaxId($userInsertId, $customerInsertedId, $customerData)
    {
        foreach (array_filter($customerData['taxId']) as $tax) {
            DB::table('customer_tax_id_tbl')->insert([
                'tax_id' => $tax,
                'customer_id' => $customerInsertedId,
                'customer_user_id' => $userInsertId,
            ]);
        }
    }

    private function insertAccesslog($customerData)
    {
        $accesslogInfo = [
            'action_page' => $customerData['actionPage'],
            'action_done' => $customerData['actionDone'],
            'remarks' => $customerData['remarks'],
            'user_name' => auth()->id(), // Assuming you are using Laravel's authentication
            'level_id' => $this->level_id, // Assuming you have a 'level_id' field in your user table
            'ip_address' => request()->ip(),
            'entry_date' => now(),
        ];

        DB::table('accesslog')->insert($accesslogInfo);
    }
}
