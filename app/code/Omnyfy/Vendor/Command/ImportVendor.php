<?php
/**
 * Project: Omnyfy Multi Vendor.
 * User: jing
 * Date: 7/4/17
 * Time: 12:16 PM
 */
namespace Omnyfy\Vendor\Command;

use Omnyfy\Vendor\Model\Resource\Vendor as VendorResource;
use Omnyfy\Core\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Magento\Framework\File\Csv;
use Magento\Framework\ObjectManagerInterface;
use Magento\User\Model\UserFactory;
use Omnyfy\Vendor\Api\Data\VendorInterface;


class ImportVendor extends Command
{
    protected $csvProcessor;

    protected $vendorResource;

    protected $userFactory;

    protected $omnyfyHelper;

    protected $objectManager;

    public function __construct(
        Csv $csvProcessor,
        VendorResource $vendorResource,
        UserFactory $userFactory,
        \Omnyfy\Vendor\Helper\Data $omnyfyHelper,
        ObjectManagerInterface $objectManager)
    {
        $this->csvProcessor = $csvProcessor;
        $this->vendorResource = $vendorResource;
        $this->userFactory = $userFactory;
        $this->omnyfyHelper = $omnyfyHelper;
        $this->objectManager = $objectManager;
    }

    protected function configure()
    {
        $this->setName('omnyfy:vendor:import');
        $this->setDescription('Import vendors from CSV file');
        $this->addArgument('filename', InputArgument::REQUIRED, 'Filename ( abcd.csv )');
        parent::configure();
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    )
    {
        //file name from input
        $filename = $input->getArgument('filename');

        //read from file and organise array for bulk save
        $csvRows = $this->csvProcessor->getData($filename);

        //validation rules column => validators
        // validator => message, or validator => [option array]

        $rules = [
            'name' => ['\Magento\Framework\Validator\NotEmpty' => 'Name is empty' ],
            'status' => ['\Magento\Framework\Validator\Int' => 'Invalid status'],
            'address' =>
                [
                    '\Magento\Framework\Validator\NotEmpty' => 'Address is empty',
                    '\Magento\Framework\Validator\StringLength' => ['min' => 5, 'max' => 255]
                ],
            'phone' => [],
            'email' => ['\Magento\Framework\Validator\EmailAddress' => 'Invalid email'],
            'fax' => [],
            'social_media' => ['\Magento\Framework\Validator\Uri'],
            'description' => [],
            'abn' => ['\Magento\Framework\Validator\NotEmpty' => 'ABN is empty'],
            'logo' => ['\Magento\Framework\Validator\Uri' => 'Invalid logo'],
            'banner' => ['\Magento\Framework\Validator\Uni' => 'Invalid banner']
        ];

        $data = [];
        $errors = [];

        $roleIds = $this->omnyfyHelper->getRoleIdsByName(VendorInterface::VENDOR_ADMIN_ROLE);
        if (empty($roleIds)) {
            $errors[0][] = 'Role '. VendorInterface::VENDOR_ADMIN_ROLE . ' not exist.';
        }

        $header = $csvRows[0];

        //validate header, CSV headers have to be the same as database columns
        foreach($rules as $col => $rule){
            if (!in_array($col, $header)) {
                $errors[0][] = 'Column '. $col. ' is missing.';
            }
        }

        //validate data array for vendors
        foreach($csvRows as $key => $row) {
            if (0 == $key) continue;

            $current = array_combine($header, $row);

            if ($this->validate($current, $rules, $key+1, $errors)) {
                $data[] = $current;
            }
        }

        if (count($errors) > 0) {
            //output all errors
            $output->writeln('ERROR:');
            foreach($errors as $lineNumber => $errorArray) {
                foreach($errorArray as $msg) {
                    $output->writeln('Line '.$lineNumber.': ' . $msg);
                }
            }
            return;
        }

        //bulk save vendors in chunks
        $this->vendorResource->bulkSave($data);

        //for each vendor, check if a vendor admin account been created. Create if not. Leave inactive by default.
        $userCollection = $this->userFactory->create()->getCollection();
        $userNames = [];
        foreach($userCollection as $user) {
            if ($user->getRole() == VendorInterface::VENDOR_ADMIN_ROLE) {
                $userNames[] = $user->getUsername();
            }
        }
        //use vendor name as vendor admin username
        foreach($data as $row) {
            if (in_array($row['name'], $userNames)) {
                continue;
            }
            $user = $this->userFactory->create();
            $user->setData([
                'username' => $row['name'],
                'firstname' => $row['name'],
                'lastname' => 'admin',
                'email' => $row['email'],
                'password' => $row['email'],
                'is_active' => 0
            ]);

            $user->setRoleId($roleIds[0]);
            $user->save();
        }

        $output->writeln('SUCCESS: '. count($data) . ' vendors imported.');
    }

    protected function validate($data, $rules, $lineNumber, &$errors) {
        $result = true;
        foreach($data as $key => $value) {
            if (!isset($rules[$key]) || empty($rules[$key])) {
                continue;
            }

            foreach($rules[$key] as $className => $option) {
                if (is_array($option)) {
                    $validator = $this->objectManager->create($className, $option);

                    if (!$validator->isValid($value)) {
                        $errors[$lineNumber] = isset($errors[$lineNumber]) ? $errors[$lineNumber] : [];
                        $errors[$lineNumber] += $validator->getMessages();
                        $result = false;
                    }
                }
                elseif (is_string($option)){
                    $validator = $this->objectManager->create($className);
                    if (!$validator->isValid($value)) {
                        $errors[$lineNumber] = isset($errors[$lineNumber]) ? $errors[$lineNumber]  + [$option]: [$option];
                        $result = false;
                    }
                }
            }
        }

        return $result;
    }
}