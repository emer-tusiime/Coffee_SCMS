public class Main {
    public static void main(String[] args) {
        if (args.length < 1) {
            System.out.println("Usage: java Main <vendorName>");
            return;
        }

        String vendorName = args[0];
        boolean isValid = VendorValidator.validateVendorName(vendorName);

        if (isValid) {
            System.out.println("Vendor name '" + vendorName + "' is valid.");
        } else {
            System.out.println("Vendor name '" + vendorName + "' is INVALID.");
        }
    }
}